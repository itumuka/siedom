<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KelasController extends Controller
{
    //Data Kelas Untuk Kelas/Index
    public function getDataKelas()
    {
        try {
            $id_mreg = Session::get('id_mreg');
    
            // 1) Ambil tahun & semester dari akd_mreg
            $mreg = DB::table('akd_mreg')
                ->select('tahun', 'semester')
                ->where('id_mreg', $id_mreg)
                ->first();
    
            // 2) Data master kelas, difilter periode yang sama
            $kelasData = DB::table('akd_kelas_kuliah')
                ->select(
                    'akd_kelas_kuliah.id_kelas',
                    'akd_kelas_kuliah.nama_kelas',
                    'akd_kelas_kuliah.kode_dosen',
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'akd_program_studi.nama_program_studi',
                    'akd_penawaran_matakuliah.smt_matakuliah',
                    'simpeg_pegawai.nama'
                )
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah',           'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
                ->join('simpeg_pegawai',           'akd_penawaran_matakuliah.kode_dosen',   '=', 'simpeg_pegawai.id')
                ->join('akd_program_studi',        'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                // <<< Filter by active tahun & semester
                ->where('akd_penawaran_matakuliah.tahun',   $mreg->tahun)
                ->where('akd_penawaran_matakuliah.semester',$mreg->semester)
                ->get();
    
            // 3) Ambil agregasi jawaban untuk periode itu
            $totalJawaban = DB::table('edom_jawaban')
                ->select(
                    'id_kelas',
                    DB::raw('COUNT(jawaban) as total_jawaban'),
                    DB::raw('COUNT(DISTINCT user_id) as total_mahasiswa')
                )
                ->where('id_mreg', $id_mreg)
                ->groupBy('id_kelas')
                ->get();
    
            // 4) Merge
            $data = $kelasData->map(function ($kelas) use ($totalJawaban) {
                $jawaban = $totalJawaban->firstWhere('id_kelas', $kelas->id_kelas);
                $kelas->total_jawaban   = $jawaban->total_jawaban ?? 0;
                $kelas->total_mahasiswa = $jawaban->total_mahasiswa ?? 0;
                return $kelas;
            });
    
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function getChartData($id_kelas)
    {
        try {
            $query = DB::table('edom_jawaban')
                ->select('id_soal', 'jawaban', DB::raw('COUNT(jawaban) as total_jawaban'))
                ->where('id_kelas', $id_kelas)
                ->groupBy('id_soal', 'jawaban')
                ->orderBy('id_soal')
                ->get();
            
            $data = [];
            foreach ($query as $row) {
                $data[$row->id_soal]['jawaban'][$row->jawaban] = $row->total_jawaban;
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllSoalData($id_kelas)
    {
        $id_mreg = Session::get('id_mreg');
    
        $raw = DB::table('edom_jawaban')
            ->join('edom_soal', 'edom_jawaban.id_soal', '=', 'edom_soal.id_soal')
            ->where('edom_jawaban.id_kelas', $id_kelas)
            ->where('edom_jawaban.id_mreg',   $id_mreg)
            ->select(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_jawaban.jawaban',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_jawaban.jawaban'
            )
            ->orderBy('edom_soal.id_soal')
            ->get();
    
        return response()->json(['data' => $raw]);
    }
    
    public function getJawabanKelasData($id_kelas)
    {
        $id_mreg = Session::get('id_mreg');
    
        $chartData = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->select(
                'akd_matakuliah.nama_matakuliah',
                'akd_matakuliah.kode_matakuliah',
                'edom_jawaban.jawaban',
                DB::raw('COUNT(*) as count'),
                'simpeg_pegawai.nama as nama_dosen'
            )
            ->where('edom_jawaban.id_kelas', $id_kelas)
            ->where('edom_jawaban.id_mreg',   $id_mreg)         // ← filter by session id_mreg
            ->groupBy(
                'akd_matakuliah.nama_matakuliah',
                'akd_matakuliah.kode_matakuliah',
                'edom_jawaban.jawaban',
                'simpeg_pegawai.nama'
            )
            ->get();
    
        $total_students = DB::table('edom_jawaban')
            ->where('id_kelas', $id_kelas)
            ->where('id_mreg',   $id_mreg)                       // ← sama di sini
            ->distinct('user_id')
            ->count('user_id');
    
        $total_responses = $chartData->sum('count');
    
        return response()->json([
            'data'             => $chartData,
            'total_students'   => $total_students,
            'total_responses'  => $total_responses
        ]);
    }
    

    public function getDosenCourses()
    {
        $id_pegawai = Session::get('id_pegawai');
        $id_mreg     = Session::get('id_mreg');
    
        // Ambil tahun & semester dari akd_mreg
        $mreg = DB::table('akd_mreg')
            ->select('tahun', 'semester')
            ->where('id_mreg', $id_mreg)
            ->first();
    
        try {
            $query = DB::table('akd_kelas_kuliah')
                ->select(
                    'akd_kelas_kuliah.id_kelas',
                    'akd_kelas_kuliah.nama_kelas',
                    'akd_kelas_kuliah.kode_dosen',
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'akd_program_studi.nama_program_studi',
                    'akd_penawaran_matakuliah.smt_matakuliah',
                    'simpeg_pegawai.nama',
                    DB::raw('COUNT(edom_jawaban.jawaban) as total_jawaban'),
                    DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mahasiswa')
                )
                ->leftJoin('edom_jawaban', 'akd_kelas_kuliah.id_kelas', '=', 'edom_jawaban.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
                ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
                ->join('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
    
                // HANYA untuk tahun & semester yang sama dengan mreg session
                ->where('akd_penawaran_matakuliah.tahun',   $mreg->tahun)
                ->where('akd_penawaran_matakuliah.semester',$mreg->semester)
    
                ->where('simpeg_pegawai.id', $id_pegawai)
                ->groupBy(
                    'akd_kelas_kuliah.id_kelas',
                    'akd_kelas_kuliah.nama_kelas',
                    'akd_kelas_kuliah.kode_dosen',
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'simpeg_pegawai.nama',
                    'akd_program_studi.nama_program_studi',
                    'akd_penawaran_matakuliah.smt_matakuliah'
                )
                ->get();
    
            return response()->json(['data' => $query]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //CHANGE

    public function getJawabanPerSoalDosenPaginated(Request $request)
    {
        $id_pegawai = Session::get('id_pegawai');
        $id_mreg    = Session::get('id_mreg');
        $perPage    = $request->input('per_page', 10);
        $page       = $request->input('page', 1);

        // Ambil tahun & semester dari akd_mreg
        $mreg = DB::table('akd_mreg')
            ->select('tahun', 'semester')
            ->where('id_mreg', $id_mreg)
            ->first();

        // Ambil semua soal yang punya jawaban (gabungan semua matkul dosen login)
        $soalList = DB::table('edom_soal')
            ->join('edom_jawaban', 'edom_soal.id_soal', '=', 'edom_jawaban.id_soal')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_pegawai)
            ->where('akd_penawaran_matakuliah.tahun', $mreg->tahun)
            ->where('akd_penawaran_matakuliah.semester', $mreg->semester)
            ->select('edom_soal.id_soal', 'edom_soal.pertanyaan')
            ->distinct()
            ->get();

        $total = $soalList->count();
        $lastPage = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $currentSoal = $soalList->slice($offset, $perPage)->values();

        $soalIds = $currentSoal->pluck('id_soal')->toArray();

        $jawabanData = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_pegawai)
            ->where('akd_penawaran_matakuliah.tahun', $mreg->tahun)
            ->where('akd_penawaran_matakuliah.semester', $mreg->semester)
            ->whereIn('edom_jawaban.id_soal', $soalIds)
            ->select(
                'edom_jawaban.id_soal',
                'edom_jawaban.jawaban',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('edom_jawaban.id_soal', 'edom_jawaban.jawaban')
            ->get();

        $result = [];
        foreach ($currentSoal as $soal) {
            $jawabanBreakdown = $jawabanData->where('id_soal', $soal->id_soal);

            $totalScore = 0;
            $totalCount = 0;
            $answers = [0, 0, 0, 0, 0];

            foreach ($jawabanBreakdown as $row) {
                $answers[$row->jawaban] = $row->count;
                $totalScore += $row->jawaban * $row->count;
                $totalCount += $row->count;
            }
            $average = $totalCount ? $totalScore / $totalCount : 0;
            $totalPercentage = $totalCount ? ($average / 4) * 100 : 0;

            $result[] = [
                'id_soal'        => $soal->id_soal,
                'pertanyaan'     => $soal->pertanyaan,
                'answers'        => $answers,
                'total_count'    => $totalCount,
                'average'        => round($average, 2),
                'total_percentage' => round($totalPercentage, 2)
            ];
        }

        return response()->json([
            'data' => $result,
            'pagination' => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'per_page'     => $perPage,
                'total'        => $total
            ]
        ]);
    }
      
    
    


}
