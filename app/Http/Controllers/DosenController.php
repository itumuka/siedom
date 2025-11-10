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



 
    
}
