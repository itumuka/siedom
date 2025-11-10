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
        // Query untuk mengambil data kelas dan informasi terkait
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
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->join('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->get();

        // Query untuk mengambil total jawaban dan total mahasiswa dari tabel edom_jawaban
        $totalJawaban = DB::table('edom_jawaban')
            ->select(
                'id_kelas',
                DB::raw('COUNT(jawaban) as total_jawaban'),
                DB::raw('COUNT(DISTINCT user_id) as total_mahasiswa')
            )
            ->groupBy('id_kelas')
            ->get();

        // Menggabungkan data kelas dengan data agregat berdasarkan id_kelas
        $data = $kelasData->map(function ($kelas) use ($totalJawaban) {
            $jawaban = $totalJawaban->firstWhere('id_kelas', $kelas->id_kelas);
            $kelas->total_jawaban = $jawaban ? $jawaban->total_jawaban : 0;
            $kelas->total_mahasiswa = $jawaban ? $jawaban->total_mahasiswa : 0;
            return $kelas;
        });

        return response()->json(['data' => $data]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()],500);
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

    public function getJawabanKelasData($id_kelas)
    {
        try {
            // Fetch data for chart and counts
            $chartData = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
                ->select(
                    'akd_matakuliah.nama_matakuliah',
                    'akd_matakuliah.kode_matakuliah',
                    'edom_jawaban.jawaban',
                    DB::raw('COUNT(edom_jawaban.jawaban) as count')
                )
                ->where('akd_kelas_kuliah.id_kelas', $id_kelas)
                ->groupBy('akd_matakuliah.nama_matakuliah', 'akd_matakuliah.kode_matakuliah', 'edom_jawaban.jawaban')
                ->get();
    
            // Calculate total students who responded (based on unique student IDs in the class)
            $total_students = DB::table('edom_jawaban')
                ->where('id_kelas', $id_kelas)
                ->distinct('user_id')
                ->count('user_id'); // Count distinct students
    
            // Total number of responses (sum of all answers)
            $total_responses = $chartData->sum('count');
    
            return response()->json([
                'data' => $chartData->map(function ($item) {
                    return [
                        'nama_matakuliah' => $item->nama_matakuliah,
                        'kode_matakuliah' => $item->kode_matakuliah,
                        'jawaban' => $item->jawaban,
                        'count' => $item->count
                    ];
                }),
                'total_students' => $total_students,
                'total_responses' => $total_responses
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    //Detail Kelas Login Dosen
    public function getDosenCourses()
    {
        $id_pegawai = Session::get('id_pegawai');
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
                // ->join('akd_program_studi', 'akd_mahasiswa.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                ->where('simpeg_pegawai.id', $id_pegawai)
                ->groupBy('akd_kelas_kuliah.id_kelas', 'akd_kelas_kuliah.nama_kelas', 'akd_kelas_kuliah.kode_dosen', 'akd_matakuliah.nama_matakuliah', 'akd_matakuliah.kode_matakuliah', 'simpeg_pegawai.nama', 'akd_program_studi.nama_program_studi', 'akd_penawaran_matakuliah.smt_matakuliah' )
                ->get();

            return response()->json([
                'data' => $query
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }   
    
    


}
