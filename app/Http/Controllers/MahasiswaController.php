<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mreg;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MahasiswaController extends Controller
{
    public function index()
    {
        $title = 'Dashboard'; 
        $parent_breadcrumb = 'Dashboard';
        return view('mahasiswa.dashboard', compact('title', 'parent_breadcrumb'));
    }
    // public function show($id)
    // {
    //     $token = Session::get('token');
    //     $userlogin = Session::get('username');
    
    //     $client = new \GuzzleHttp\Client();
    //     $response = $client->request('GET', config('setting.second_url') . 'mahasiswa/tampil-presensi-makul', [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $token,
    //             'username' => $userlogin
    //         ],
    //         'query' => [
    //             'id_kelas' => $id
    //         ]
    //     ]);
    
    //     $data = json_decode($response->getBody(), true);
    
    //     $detail = $data[0]; // assuming the first element is the required detail

    //     return view('mahasiswa.soal', compact('detail'));
    // }
    public function show()
    {
        $title = 'Soal';
        return view('mahasiswa.soal', compact('title'));
    }

    public function getKomponenPenilaian()
    {
        try {
            $komponenPenilaian = DB::table('komponen_penilaian')->get();
            return response()->json($komponenPenilaian);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }

    public function getSoal(Request $request)
    {
        try {
            $soal = DB::table('soal')->get();
            return response()->json($soal);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }
    
    public function store(Request $request)
    {
        $answers = $request->all();
        
        foreach ($answers as $answer) {
            DB::table('jawaban')->insert([
                'id_soal' => $answer['id_soal'],
                'user_id' => $answer['user_id'],
                'id_mreg' => $answer['id_mreg'],
                'id_kelas' => $answer['id_kelas'],
                'jawaban' => $answer['jawaban'],
                'timestamp' => now()
            ]);
        }
        
        return response()->json(['message' => 'Jawaban saved successfully']);
    }
}
