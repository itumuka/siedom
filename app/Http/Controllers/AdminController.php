<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\KelasController;

class AdminController extends Controller
{
    public function index()
    {
        $title = 'Dashboard'; 
        $parent_breadcrumb = 'Dashboard';
        $tahun_ajaran = Session::get('session_nama_tahunakademik');
        return view('admin.dashboard', compact('title', 'parent_breadcrumb', 'tahun_ajaran'));
    }

    public function sudahJawab()
    {
        return view('admin.jawaban.index');
    }
    
    public function belumJawab()
    {
        return view('admin.jawaban.belum_jawab');
    }

    public function kelas_list()
    {
        return view('admin.kelas.index');
    }

    public function change_session(Request $request)
    {
        Session::put('session_tahun', $request->tahun);
        Session::put('session_semester', $request->semester);
        Session::put('session_nama_tahunakademik', $request->tahun_ajaran);
        Session::put('id_mreg', $request->id_mreg);

        return true;
    }

    public function getsession_ta()
    {
        $session_nama_tahunakademik = (Session::has('session_nama_tahunakademik')) ? Session::get('session_nama_tahunakademik') : '';
        $ket = 'Tahun Akademik Aktif saat ini ' . $session_nama_tahunakademik;
        return response()->json(array('ket' => $ket), 200);

        // return response()->json(['ket' => $ket]);
    }

    public function reportIndex()
    {

        return view('admin.report.index');
    }

    public function reportSoal()
    {

        return view('admin.report.persoal');
    }

    public function detailKelasChart($id_kelas)
    {
        try {
            // Get instance of KelasController using dependency injection
            $kelasController = App::make(KelasController::class);
            $chartData = $kelasController->getJawabanKelasData($id_kelas);
    
            return view('admin.kelas.detail_kelas', [
                'chartData' => $chartData,
                'id_kelas' => $id_kelas
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function averagePage($id_kelas)
    {
        return view('admin.kelas.average', ['id_kelas' => $id_kelas]);
    }
    
    public function getFakultas()
    {
        $data = DB::table('akd_fakultas')->select('kode_fakultas', 'nama_fakultas')->get();
        return response()->json($data);
    }

    public function getProdi()
    {
        $data = DB::table('akd_program_studi')->select('kode_program_studi', 'nama_program_studi')->get();
        return response()->json($data);
    }
}