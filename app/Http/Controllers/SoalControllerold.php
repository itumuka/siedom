<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SoalController extends Controller
{
    public function index()
    {
        return view('admin.soal.index');
    }

    public function getData()
    {
        try {
            $query = DB::table('edom_soal')
            ->select('edom_soal.*', 'edom_komponen_penilaian.nama_komponen', DB::raw("CONCAT_WS(' ', akd_mreg.tahun_akademik, IF(akd_mreg.semester = '1', 'Ganjil', 'Genap')) AS tahun_ajaran"))
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->leftJoin('akd_mreg', 'edom_soal.id_mreg', '=', 'akd_mreg.id_mreg');
            
            $totalRecords = $query->count();
    
            $data = $query->get();
    
            return response()->json([
                'draw' => intval(request()->get('draw')),
                'recordsTotal' => $totalRecords,
                'records    Filtered' => $totalRecords,
                'data' => $query->get()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }
    

    public function store(Request $request)
    {
        try {
            $id_mreg = Session::get('id_mreg');

            DB::table('edom_soal')->insert([
                'pertanyaan' => $request->pertanyaan,
                'id_komponen_penilaian' => $request->id_komponen_penilaian,
                'id_mreg' => $request->id_mreg,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['message' => 'Data berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambah data'], 500);
        }
    }

    public function show($id)
    {
        $soal = DB::table('edom_soal')->where('id_soal', $id)->first();
        return response()->json(['data' => $soal]);
    }

    public function update(Request $request, $id)
    {
        try {
            $id_mreg = Session::get('id_mreg');

            DB::table('edom_soal')
                ->where('id_soal', $id)
                ->update([
                    'pertanyaan' => $request->pertanyaan,
                    'id_komponen_penilaian' => $request->id_komponen_penilaian,
                    'id_mreg' => $request->id_mreg,
                    'updated_at' => now()
                ]);

            return response()->json(['message' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            DB::table('edom_jawaban')->where('id_soal', $id)->delete();

            DB::table('edom_soal')->where('id_soal', $id)->delete();
    
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDataMreg()
    {
    try {
        $mreg = DB::table('akd_mreg')
            ->select(DB::raw("*, IF(semester='1', CONCAT_WS(' ', tahun_akademik, 'Ganjil'), CONCAT_WS(' ', tahun_akademik, 'Genap')) AS tahun_ajaran"))
            ->orderBy('tahun', 'DESC')
            ->get();

        $totalRecords = DB::table('akd_mreg')->count();

        return response()->json([
            'draw' => intval(request()->get('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $mreg
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Data tidak tersedia.'], 500);
    }
    }


}
