<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KomponenPenilaianController extends Controller
{
    public function index()
    {
        return view('admin.komponen_penilaian.index');
    }

    public function getData()
    {
        try {
            $komponenPenilaian = DB::table('edom_komponen_penilaian')->get();
            $totalRecords = DB::table('edom_komponen_penilaian')->count();

            return response()->json([
                'draw' => intval(request()->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $komponenPenilaian
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_komponen' => 'required|string|max:255',
            ]);

            $id = DB::table('edom_komponen_penilaian')->insertGetId([
                'nama_komponen' => $validated['nama_komponen'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['id' => $id, 'nama_komponen' => $validated['nama_komponen']]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambah data.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DB::table('edom_komponen_penilaian')->where('id_komponen_penilaian', $id)->first();
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_komponen' => 'required|string|max:255',
            ]);

            DB::table('edom_komponen_penilaian')->where('id_komponen_penilaian', $id)->update([
                'nama_komponen' => $validated['nama_komponen'],
                'updated_at' => now(),
            ]);

            return response()->json(['id' => $id, 'nama_komponen' => $validated['nama_komponen']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $count = DB::table('edom_soal')->where('id_komponen_penilaian', $id)->count();
            if ($count > 0) {
                return response()->json(['error' => 'Data tidak bisa dihapus karena ada ketergantungan di tabel soal.'], 400);
            }

            // Delete the record
            DB::table('edom_komponen_penilaian')->where('id_komponen_penilaian', $id)->delete();
            return response()->json(['success' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting komponen_penilaian: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus data.'], 500);
        }
    }

    
}
