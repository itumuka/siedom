<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function index()
    {
        $title = 'Dashboard'; 
        $parent_breadcrumb = 'Dashboard';
        return view('admin.dashboard', compact('title', 'parent_breadcrumb'));
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
}