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
            // kaprodi flag: payload dari login JS menggunakan key "kaprodi" (result.data.pimpinan_prodi)
        $kaprodi = $request->kaprodi ?? $request->pimpinan_prodi ?? null;
        Session::put('kaprodi', $kaprodi);
        Session::put('is_kaprodi', !empty($kaprodi));
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

    public function ssoLogin(Request $request)
    {
        $payload = $request->query('payload');
        $sig = $request->query('sig');
        $secret = env('SSO_SIEDOM_SECRET', 'sso-secret-siedom-123');
        
        if (!$payload || !$sig) {
            return redirect(route('login'))->with('error', 'Token SSO tidak ditemukan');
        }

        $expected_sig = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected_sig, $sig)) {
            return redirect(route('login'))->with('error', 'SSO Signature tidak valid');
        }
        
        $data = json_decode(base64_decode($payload), true);
        if (time() - $data['ts'] > 300) { // 5 minutes expiry
            return redirect(route('login'))->with('error', 'Link SSO sudah kadaluarsa');
        }

        // Override tahun & semester dengan request jika ada (dari dropdown filter)
        if ($request->has('req_tahun') && $request->has('req_semester')) {
            $data['tahun'] = $request->query('req_tahun');
            $data['semester'] = $request->query('req_semester');
            unset($data['id_mreg']); // Hapus id_mreg lama agar diganti sesuai tahun & semester baru
            unset($data['tahun_ajaran']); // Hapus nama tahun ajaran lama
        }

        // Jika id_mhs kosong, cari di database berdasarkan NIM
        if (empty($data['id_mhs'])) {
            $mhs = DB::table('akd_mahasiswa')->where('nim', $data['nim'])->first();
            if ($mhs) {
                $data['id_mhs'] = $mhs->id_mhs;
                $data['nama'] = $mhs->nama_mahasiswa;
                $data['gender'] = $mhs->jenis_kelamin;
                if (empty($data['kode_program_studi'])) {
                    $data['kode_program_studi'] = $mhs->kode_program_studi;
                }
            }
        }

        // Jika id_mreg kosong, cari berdasarkan tahun & semester
        if (empty($data['id_mreg'])) {
            $mreg = DB::table('akd_mreg')->where('tahun', $data['tahun'])->where('semester', $data['semester'])->first();
            if ($mreg) {
                $data['id_mreg'] = $mreg->id_mreg;
                $smt_label = $mreg->semester == '1' ? 'Semester Ganjil' : 'Semester Genap';
                $data['tahun_ajaran'] = $smt_label . ' ' . $mreg->tahun_akademik;
            }
        }
        
        Session::put('session_tahun', $data['tahun']);
        Session::put('session_semester', $data['semester']);
        Session::put('session_nim', $data['nim']);
        Session::put('session_nama_tahunakademik', $data['tahun_ajaran']);
        Session::put('tipe', 'Mahasiswa');
        Session::put('username', $data['username']);
        Session::put('gender', $data['gender']);
        Session::put('nama', $data['nama']);
        Session::put('kode_program_studi', $data['kode_program_studi']);
        Session::put('token', $data['token']);
        Session::put('id_mhs', $data['id_mhs']);
        Session::put('id_mreg', $data['id_mreg']);
        
        return redirect('/home');
    }
}
