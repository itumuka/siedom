<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{

    // Mengembalikan statistik mahasiswa yang sudah / belum mengisi
    public function getMahasiswaJawabanChart()
    {
        try {
            $totalStudents = DB::table('akd_mahasiswa')->count();
            $completedStudents = DB::table('edom_jawaban')->distinct('user_id')->count('user_id');
            $notCompletedStudents = $totalStudents - $completedStudents;

            return response()->json([
                'completed_students' => $completedStudents,
                'not_completed_students' => $notCompletedStudents,
            ]);
        } catch (\Exception $e) {
            Log::error('getMahasiswaJawabanChart error: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function prodiReportView()
    {
        return view('admin.report.prodi');
    }

    public function reportProdiUniversal(Request $request)
    {
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        if (!$kode_prodi || !$id_mreg) {
            Log::warning('reportProdiUniversal: missing session', ['kode_program_studi'=>$kode_prodi,'id_mreg'=>$id_mreg]);
            return response()->json(['pieData'=>[], 'total'=>0, 'message'=>'Session kode_program_studi atau id_mreg belum diset']);
        }

        $labels = [0=>'Tidak Berlaku',1=>'Sangat Tidak Sesuai',2=>'Tidak Sesuai',3=>'Sesuai',4=>'Sangat Sesuai'];

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $total = $rows->sum('count');
        $pieData = [];
        foreach ($labels as $k => $v) {
            $r = $rows->firstWhere('jawaban', $k);
            $cnt = $r ? $r->count : 0;
            $pieData[] = ['name'=>$v,'value'=>$cnt,'percentage'=> $total ? round(($cnt/$total)*100,2) : 0];
        }

        return response()->json(['pieData'=>$pieData,'total'=>$total]);
    }

    public function reportProdiPerDosen(Request $request)
    {
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        if (!$kode_prodi || !$id_mreg) {
            Log::warning('reportProdiPerDosen: missing session', ['kode_program_studi'=>$kode_prodi,'id_mreg'=>$id_mreg]);
            return response()->json(['list'=>[], 'message'=>'Session kode_program_studi atau id_mreg belum diset']);
        }

        try {
            $rows = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
                ->join('simpeg_pegawai','akd_penawaran_matakuliah.kode_dosen','=','simpeg_pegawai.id')
                ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
                ->where('edom_jawaban.id_mreg', $id_mreg)
                ->select(
                    'simpeg_pegawai.id as id_pegawai',
                    'simpeg_pegawai.nama as nama',
                    'simpeg_pegawai.nip as nip',
                    DB::raw('AVG(edom_jawaban.jawaban) as avg_score'),
                    DB::raw('COUNT(*) as total_responses')
                )
                ->groupBy('simpeg_pegawai.id','simpeg_pegawai.nama','simpeg_pegawai.nip')
                ->orderBy('avg_score','desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('reportProdiPerDosen query error: '.$e->getMessage());
            return response()->json(['list'=>[], 'message'=>'Query error, cek log.']);
        }

        $list = $rows->map(function($r){
            return [
                'id_pegawai' => $r->id_pegawai,
                'nama'       => $r->nama,
                'nip'        => $r->nip,
                'avg'        => round($r->avg_score,2),
                'percent'    => round(($r->avg_score/4)*100,2),
                'responses'  => (int)$r->total_responses
            ];
        })->values();

        return response()->json(['list'=>$list]);
    }

    public function reportProdiPerKelas(Request $request)
    {
        $id_dosen = $request->input('id_dosen');
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        if (!$kode_prodi || !$id_mreg) {
            return response()->json(['kelas'=>[], 'message'=>'Session kode_program_studi atau id_mreg belum diset']);
        }
        if (!$id_dosen) {
            return response()->json(['kelas'=>[], 'message'=>'id_dosen required'], 400);
        }

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah','akd_penawaran_matakuliah.id_matakuliah','=','akd_matakuliah.id_matakuliah')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_dosen)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select(
                'akd_kelas_kuliah.id_kelas',
                'akd_kelas_kuliah.nama_kelas',
                'akd_matakuliah.nama_matakuliah',
                DB::raw('AVG(edom_jawaban.jawaban) as avg_score'),
                DB::raw('COUNT(*) as total_responses')
            )
            ->groupBy('akd_kelas_kuliah.id_kelas','akd_kelas_kuliah.nama_kelas','akd_matakuliah.nama_matakuliah')
            ->orderBy('avg_score','desc')
            ->get();

        $kelas = $rows->map(function($r){
            return [
                'id_kelas' => $r->id_kelas,
                'nama_kelas' => $r->nama_kelas,
                'nama_matakuliah' => $r->nama_matakuliah,
                'avg' => round($r->avg_score,2),
                'percent' => round(($r->avg_score/4)*100,2),
                'responses' => (int)$r->total_responses
            ];
        })->values();

        return response()->json(['kelas'=>$kelas]);
    }


}
