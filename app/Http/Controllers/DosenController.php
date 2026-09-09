<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class DosenController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $parent_breadcrumb = 'Dashboard';
        $tahun_ajaran = Session::get('session_nama_tahunakademik'); // misal: "2023/2024"
        $semester = Session::get('session_semester') == '1' ? 'Ganjil' : 'Genap';

        return view('dosen.dashboard', compact('title', 'parent_breadcrumb', 'tahun_ajaran', 'semester'));
    }
    public function kelas()
    {
        $title = 'Kelas'; 
        $parent_breadcrumb = 'Dashboard';
        return view('dosen.courses', compact('title', 'parent_breadcrumb'));
    }

    public function overviewSoal($id_kelas)
    {
        return view('dosen.overview_soal', ['id_kelas' => $id_kelas]);
    }

    public function detailKelasChart($id_kelas)
    {
        if (Session::get('tipe') !== 'Dosen') {
            abort(403, 'Hanya dosen yang boleh mengakses halaman ini.');
        }

        $id_pegawai = Session::get('id_pegawai');
        $isOwner = DB::table('akd_kelas_kuliah')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->where('akd_kelas_kuliah.id_kelas', $id_kelas)
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_pegawai)
            ->exists();

        if (! $isOwner) {
            abort(403, 'Anda tidak berwenang mengakses detail kelas ini.');
        }


        try {
            /** @var \App\Http\Controllers\KelasController $kelasController */
            $kelasController = App::make(KelasController::class);


            $response = $kelasController->getJawabanKelasData($id_kelas);
            $json     = $response->getData();         // stdClass { data, total_students,... }
            $chartData= $json->data;
            $totMhs   = $json->total_students ?? 0;
            $totResp  = $json->total_responses ?? 0;

            return view('dosen.detail_kelas', [
                'chartData'      => $chartData,
                'total_students' => $totMhs,
                'total_responses'=> $totResp,
                'id_kelas'       => $id_kelas
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getAllSoalData($id_kelas)
    {
        $id_mreg = Session::get('id_mreg');
    
        // Ambil data jawaban + info soal + info matakuliah + info dosen
        $raw = DB::table('edom_jawaban')
            ->join('edom_soal', 'edom_jawaban.id_soal', '=', 'edom_soal.id_soal')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen','=', 'simpeg_pegawai.id')
            ->where('edom_jawaban.id_kelas', $id_kelas)
            ->where('edom_jawaban.id_mreg',   $id_mreg)
            ->select(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_jawaban.jawaban',
                DB::raw('COUNT(*) as count'),
                'akd_matakuliah.nama_matakuliah',
                'akd_matakuliah.kode_matakuliah',
                'simpeg_pegawai.nama as nama_dosen'
            )
            ->groupBy(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_jawaban.jawaban',
                'akd_matakuliah.nama_matakuliah',
                'akd_matakuliah.kode_matakuliah',
                'simpeg_pegawai.nama'
            )
            ->orderBy('edom_soal.id_soal')
            ->get();
    
        return response()->json([
            'data'              => $raw,
            // Ambil info statis dari elemen pertama (semua baris punya nilai sama untuk matkul & dosen)
            'nama_matakuliah'   => optional($raw->first())->nama_matakuliah,
            'kode_matakuliah'   => optional($raw->first())->kode_matakuliah,
            'nama_dosen'        => optional($raw->first())->nama_dosen,
        ]);
    }

    public function getJawabanKelasData($id_kelas)
    {
        try {
            $id_mreg = Session::get('id_mreg');
    
            
            $chartData = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah',            'edom_jawaban.id_kelas',   '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah',    'akd_kelas_kuliah.id_tawar','=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah',              'akd_penawaran_matakuliah.id_matakuliah','=', 'akd_matakuliah.id_matakuliah')
                ->join('simpeg_pegawai',              'akd_penawaran_matakuliah.kode_dosen',  '=', 'simpeg_pegawai.id')
                ->select(
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'simpeg_pegawai.nama as nama_dosen',        // ← Nama dosen
                    'edom_jawaban.jawaban',
                    DB::raw('COUNT(*) as count')
                )
                ->where('edom_jawaban.id_kelas', $id_kelas)
                ->where('edom_jawaban.id_mreg',  $id_mreg)
                ->groupBy(
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'simpeg_pegawai.nama',
                    'edom_jawaban.jawaban'
                )
                ->get();
    
            // 2) Total mahasiswa unik
            $total_students = DB::table('edom_jawaban')
                ->where('id_kelas', $id_kelas)
                ->where('id_mreg',   $id_mreg)
                ->distinct('user_id')
                ->count('user_id');
    
            // 3) Total respons
            $total_responses = $chartData->sum('count');
    
            // 4) Kirim JSON
            return response()->json([
                'data'             => $chartData->map(function($item) {
                    return [
                        'nama_matakuliah' => $item->nama_matakuliah,
                        'kode_matakuliah' => $item->kode_matakuliah,
                        'nama_dosen'      => $item->nama_dosen,    // ← ini
                        'jawaban'         => $item->jawaban,
                        'count'           => $item->count
                    ];
                }),
                'total_students'   => $total_students,
                'total_responses'  => $total_responses
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDashboardData(Request $request)
    {
        $id_pegawai = Session::get('id_pegawai');
        $id_mreg    = Session::get('id_mreg');

        if (!$id_pegawai || !$id_mreg) {
            return response()->json([
                'session_active'  => false,
                'message'         => 'Sesi login dosen atau tahun akademik tidak ditemukan.',
                'pieData'         => [],
                'total'           => 0,
                'total_valid'     => 0,
                'total_na'        => 0,
                'total_mhs'       => 0,
                'total_kelas'     => 0,
                'overall_avg'     => 0,
                'overall_percent' => 0,
                'kelasList'       => []
            ]);
        }

        // 1. Ambil data mreg untuk filter tahun & semester
        $mreg = DB::table('akd_mreg')
            ->select('tahun', 'semester')
            ->where('id_mreg', $id_mreg)
            ->first();

        // 2. Query dasar jawaban untuk dosen yang sedang login
        $baseQuery = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_pegawai)
            ->where('edom_jawaban.id_mreg', $id_mreg);

        // 3. Distribusi Jawaban (Pie / Donut Chart)
        $pieRaw = (clone $baseQuery)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $labels = [
            0 => 'Tidak Berlaku',
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Sesuai',
            4 => 'Sangat Sesuai'
        ];

        $total = $pieRaw->sum('count');
        $pieData = [];
        $totalValid = 0;
        $totalNa = 0;

        foreach ($labels as $key => $label) {
            $found = $pieRaw->firstWhere('jawaban', $key);
            $count = $found ? (int)$found->count : 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;

            if ($key == 0) {
                $totalNa += $count;
            } else {
                $totalValid += $count;
            }

            $pieData[] = [
                'name'       => $label,
                'key'        => $key,
                'value'      => $count,
                'percentage' => $percentage
            ];
        }

        // 4. Ringkasan Metrik Kinerja Dosen (Eksklusikan 0 = Tidak Berlaku)
        $stats = (clone $baseQuery)
            ->select(
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as overall_avg')
            )
            ->first();

        $overallAvg     = $stats && $stats->overall_avg !== null ? round((float)$stats->overall_avg, 2) : 0;
        $overallPercent = round(($overallAvg / 4) * 100, 2);
        $totalMhs       = $stats ? (int)$stats->total_mhs : 0;

        // 5. Daftar Kelas yang Diampu Dosen pada Periode ini beserta Nilainya
        $kelasQuery = DB::table('akd_kelas_kuliah')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->join('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_pegawai);

        if ($mreg) {
            $kelasQuery->where('akd_penawaran_matakuliah.tahun', $mreg->tahun)
                       ->where('akd_penawaran_matakuliah.semester', $mreg->semester);
        }

        $kelasListRaw = $kelasQuery->select(
            'akd_kelas_kuliah.id_kelas',
            'akd_kelas_kuliah.nama_kelas',
            'akd_matakuliah.nama_matakuliah',
            'akd_matakuliah.kode_matakuliah',
            'akd_program_studi.nama_program_studi',
            'akd_penawaran_matakuliah.smt_matakuliah as semester'
        )->get();

        // Ambil data agregasi per kelas untuk dosen ini
        $kelasIds = $kelasListRaw->pluck('id_kelas')->toArray() ?: [0];
        $kelasStats = DB::table('edom_jawaban')
            ->whereIn('id_kelas', $kelasIds)
            ->where('id_mreg', $id_mreg)
            ->select(
                'id_kelas',
                DB::raw('COUNT(DISTINCT user_id) as total_mahasiswa'),
                DB::raw('COUNT(jawaban) as total_jawaban'),
                DB::raw('AVG(CASE WHEN jawaban > 0 THEN jawaban ELSE NULL END) as avg_score')
            )
            ->groupBy('id_kelas')
            ->get();

        $kelasList = $kelasListRaw->map(function($item) use ($kelasStats) {
            $stat = $kelasStats->firstWhere('id_kelas', $item->id_kelas);
            $avg = $stat && $stat->avg_score !== null ? round((float)$stat->avg_score, 2) : 0;
            return [
                'id_kelas'           => $item->id_kelas,
                'nama_kelas'         => $item->nama_kelas,
                'nama_matakuliah'    => $item->nama_matakuliah,
                'kode_matakuliah'    => $item->kode_matakuliah,
                'nama_program_studi' => $item->nama_program_studi,
                'semester'           => $item->semester,
                'total_mhs'          => $stat ? (int)$stat->total_mahasiswa : 0,
                'total_jawaban'      => $stat ? (int)$stat->total_jawaban : 0,
                'avg_score'          => $avg,
                'percent'            => round(($avg / 4) * 100, 2)
            ];
        });

        return response()->json([
            'session_active'  => true,
            'pieData'         => $pieData,
            'total'           => $total,
            'total_valid'     => $totalValid,
            'total_na'        => $totalNa,
            'total_mhs'       => $totalMhs,
            'total_kelas'     => $kelasListRaw->count(),
            'overall_avg'     => $overallAvg,
            'overall_percent' => $overallPercent,
            'kelasList'       => $kelasList
        ]);
    }
}
