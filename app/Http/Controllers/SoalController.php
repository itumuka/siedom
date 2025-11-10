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

    public function duplicate(Request $request)
    {
        $sourceYear = $request->input('sourceYear');
        $targetYear = $request->input('targetYear');
    
        if (!$sourceYear || !$targetYear) {
            return response()->json(['message' => 'Tahun akademik asal dan tujuan harus dipilih'], 400);
        }
    
        try {
            // Ambil semua soal dari tahun akademik asal
            $sourceSoal = DB::table('edom_soal')
                ->where('id_mreg', $sourceYear)
                ->get();
    
            // Loop untuk menyimpan soal ke tahun akademik tujuan
            foreach ($sourceSoal as $soal) {
                DB::table('edom_soal')->insert([
                    'pertanyaan' => $soal->pertanyaan,
                    'id_komponen_penilaian' => $soal->id_komponen_penilaian,
                    'id_mreg' => $targetYear,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            return response()->json(['message' => 'Soal berhasil diduplikat'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroyByMreg($id_mreg)
    {
        try {
            DB::beginTransaction();

            // Hapus jawaban yang terkait soal di tahun akademik ini
            $soalIds = DB::table('edom_soal')->where('id_mreg', $id_mreg)->pluck('id_soal');
            DB::table('edom_jawaban')->whereIn('id_soal', $soalIds)->delete();

            // Hapus soal
            DB::table('edom_soal')->where('id_mreg', $id_mreg)->delete();

            DB::commit();
            return response()->json(['message' => 'Semua soal pada tahun akademik ini berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    public function reportPerSoal(Request $request)
    {
        $id_soal = $request->input('id_soal');
        if (!$id_soal) {
            return response()->json(['error' => 'ID soal wajib diisi'], 400);
        }

        // Pie chart data
        $labels = [
            0 => 'Tidak Berlaku',
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Sesuai',
            4 => 'Sangat Sesuai'
        ];

        $pieRaw = DB::table('edom_jawaban')
            ->where('id_soal', $id_soal)
            ->select('jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('jawaban')
            ->get();

        $total = $pieRaw->sum('count');
        $pieData = [];
        foreach ($labels as $key => $label) {
            $found = $pieRaw->firstWhere('jawaban', $key);
            $count = $found ? $found->count : 0;
            $percentage = $total ? round(($count / $total) * 100, 2) : 0;
            $pieData[] = [
                'name' => $label,
                'value' => $count,
                'percentage' => $percentage
            ];
        }

        // Top & Bottom List (rata-rata per dosen untuk soal ini)
        $scoreRaw = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->where('edom_jawaban.id_soal', $id_soal)
            ->select(
                'simpeg_pegawai.nama',
                'simpeg_pegawai.nip',
                DB::raw('AVG(edom_jawaban.jawaban) as avg_score')
            )
            ->groupBy('simpeg_pegawai.id', 'simpeg_pegawai.nama', 'simpeg_pegawai.nip')
            ->orderBy('avg_score', 'desc')
            ->get();

        $topList = $scoreRaw->take(3)->map(function($row){
            return [
                'nama' => $row->nama,
                'nip' => $row->nip,
                'nilai' => round($row->avg_score,2)
            ];
        })->values();

        $bottomList = $scoreRaw->sortBy('avg_score')->take(3)->map(function($row){
            return [
                'nama' => $row->nama,
                'nip' => $row->nip,
                'nilai' => round($row->avg_score,2)
            ];
        })->values();

        return response()->json([
            'pieData' => $pieData,
            'total' => $total,
            'topList' => $topList,
            'bottomList' => $bottomList
        ]);
    }

    public function getSoalForReport(Request $request)
    {
        $id_mreg = Session::get('id_mreg');
        $soal = DB::table('edom_soal')
            ->where('id_mreg', $id_mreg)
            ->orderBy('id_soal', 'asc')
            ->select('id_soal', 'pertanyaan')
            ->get();

        return response()->json($soal);
    }



}
