<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mreg;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class MahasiswaController extends Controller
{
    public function index()
    {
        $title = 'Kuisioner';   
        $parent_breadcrumb = 'Kuisioner';
        return view('mahasiswa.kuisioner', compact('title', 'parent_breadcrumb'));
    }

    public function Dashboard()
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
            $komponenPenilaian = DB::table('edom_komponen_penilaian')->get();
            return response()->json($komponenPenilaian);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }

    public function getSoal(Request $request)
    {
        $id_mreg = Session::get('id_mreg');
        $soal = DB::table('edom_soal')
            ->where('id_mreg', $id_mreg)
            ->get();

        return response()->json($soal);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.id_soal' => 'required|integer',
            'answers.*.user_id' => 'required|integer',
            'answers.*.id_mreg' => 'required|integer',
            'answers.*.id_kelas' => 'required|integer',
            'answers.*.jawaban' => 'required|integer',
        ], [
            'required' => 'Harap isi semua jawaban.',
            'integer' => 'Jawaban harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $answers = $request->input('answers');
        foreach ($answers as $answer) {
            DB::table('edom_jawaban')->insert([
                'id_soal' => $answer['id_soal'],
                'user_id' => Session::get('id_mhs'),
                'id_mreg' => Session::get('id_mreg'),
                'id_kelas' => $answer['id_kelas'],
                'jawaban' => $answer['jawaban'],
                'timestamp' => now()
            ]);
        }

        return response()->json(['message' => 'Jawaban saved successfully']);
    }

    public function checkKuisionerStatus(Request $request)
    {
        $id_mhs = Session::get('id_mhs');
        $id_mreg = Session::get('id_mreg');
    
        $completedClasses = DB::table('edom_jawaban')
            ->where('user_id', $id_mhs)
            ->where('id_mreg', $id_mreg)
            ->select('id_kelas')
            ->distinct()
            ->pluck('id_kelas')
            ->toArray();
    
        return response()->json([
            'completedClasses' => $completedClasses
        ]);
    }
}
