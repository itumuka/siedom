<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    //
    public function index()
    {

        $title = "Akademik SIAKAD UP45";
        return view('auth/login', compact('title'));
    }

    public function make_session_pegawa(Request $request)
    {

        Session::put('session_tahun', $request->tahun);
        Session::put('session_semester', $request->semester);
        Session::put('session_nama_tahunakademik', $request->tahun_ajaran);
        Session::put('tipe', 'Pegawai');
        Session::put('username', $request->username);
        Session::put('nama', $request->nama);
        Session::put('jabatan', $request->jabatan);
        Session::put('nm_module', $request->nm_module);
        Session::put('kode_fakultas', $request->kode_fakultas);
        Session::put('token', $request->token);
        Session::put('id_mreg', $request->id_mreg);

        return true;
    }


    public function make_session_mahasiswa(Request $request)
    {


        Session::put('session_tahun', $request->tahun);
        Session::put('session_semester', $request->semester);
        Session::put('session_nim', $request->nim);
        Session::put('session_nama_tahunakademik', $request->tahun_ajaran);
        Session::put('tipe', 'Mahasiswa');
        Session::put('username', $request->username);
        Session::put('gender', $request->gender);
        Session::put('nama', $request->nama);
        Session::put('kode_program_studi', $request->kode_program_studi);
        Session::put('token', $request->token);
        Session::put('id_mhs', $request->id_mhs);
        Session::put('id_mreg', $request->id_mreg);

        return true;
    }

    public function make_session_dosen(Request $request)
    {
        Session::put('session_tahun', $request->tahun);
        Session::put('session_semester', $request->semester);
        Session::put('session_nama_tahunakademik', $request->tahun_ajaran);
        Session::put('tipe', 'Dosen');
        Session::put('username', $request->username);
        Session::put('nama', $request->nama);
        Session::put('kode_program_studi', $request->kode_program_studi);
        Session::put('dosen_wali', $request->dosen_wali);
        Session::put('id_pegawai', $request->id_pegawai);
        Session::put('token', $request->token);
        Session::put('id_mreg', $request->id_mreg);
        

        return true;
    }

    public function logout()
    {
        Session::flush();
        return true;
    }
}
