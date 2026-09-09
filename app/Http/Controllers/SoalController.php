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
            $id_mreg = Session::get('id_mreg'); // ambil dari session

            $query = DB::table('edom_soal')
            ->select('edom_soal.*', 'edom_komponen_penilaian.nama_komponen', DB::raw("CONCAT_WS(' ', akd_mreg.tahun_akademik, IF(akd_mreg.semester = '1', 'Ganjil', 'Genap')) AS tahun_ajaran"))
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->leftJoin('akd_mreg', 'edom_soal.id_mreg', '=', 'akd_mreg.id_mreg');

            // tambahkan filter id_mreg agar hanya menampilkan soal untuk session aktif
            if ($id_mreg) {
                $query->where('edom_soal.id_mreg', $id_mreg);
            }
            
            $totalRecords = $query->count();
    
            $data = $query->get();
    
            return response()->json([
                'draw' => intval(request()->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak tersedia.'], 500);
        }
    }
// ...existing code...
    

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

        $id_mreg        = $request->input('id_mreg', Session::get('id_mreg'));
        $kode_fakultas  = $request->input('kode_fakultas');
        $kode_prodi     = $request->input('kode_prodi');

        if (Session::get('is_kaprodi')) {
            $kode_prodi = Session::get('kode_program_studi');
        }

        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }

        // Ambil info soal & komponen
        $soalInfo = DB::table('edom_soal')
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->where('edom_soal.id_soal', $id_soal)
            ->select(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_soal.id_komponen_penilaian',
                DB::raw('COALESCE(edom_komponen_penilaian.nama_komponen, "Umum") as nama_komponen')
            )
            ->first();

        $labels = [
            0 => 'Tidak Berlaku',
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Sesuai',
            4 => 'Sangat Sesuai'
        ];

        // Base Query untuk jawaban soal ini
        $baseQuery = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->leftJoin('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->where('edom_jawaban.id_soal', $id_soal)
            ->when($id_mreg, function($q) use ($id_mreg) {
                return $q->where('edom_jawaban.id_mreg', $id_mreg);
            })
            ->when($kode_prodi, function($q) use ($kode_prodi) {
                return $q->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi);
            })
            ->when($kode_fakultas, function($q) use ($kode_fakultas) {
                return $q->where('akd_program_studi.kode_fakultas', $kode_fakultas);
            });

        // 1. Distribusi Frekuensi Jawaban
        $pieRaw = (clone $baseQuery)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $total = $pieRaw->sum('count');
        $pieData = [];
        $totalValid = 0;
        $totalNa = 0;

        foreach ($labels as $key => $label) {
            $found = $pieRaw->firstWhere('jawaban', $key);
            $count = $found ? (int)$found->count : 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;

            if ($key == 0) {
                $totalNa += $count;
            } else {
                $totalValid += $count;
            }

            $pieData[] = [
                'jawaban'    => $key,
                'name'       => $label,
                'value'      => $count,
                'count'      => $count,
                'percentage' => $percentage
            ];
        }

        // 2. Metrik Ringkasan (Eksklusi 0 = Tidak Berlaku)
        $statQuery = (clone $baseQuery)->select(
            DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
            DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score')
        )->first();

        $avgScore = $statQuery && $statQuery->avg_score !== null ? round((float)$statQuery->avg_score, 2) : 0;
        $mutuPercent = round(($avgScore / 4) * 100, 2);
        $totalMhs = $statQuery ? (int)$statQuery->total_mhs : 0;

        $predikat = 'Kurang';
        $badgeClass = 'danger';
        if ($avgScore >= 3.50) {
            $predikat = 'Sangat Sesuai';
            $badgeClass = 'success';
        } elseif ($avgScore >= 3.00) {
            $predikat = 'Sesuai';
            $badgeClass = 'info';
        } elseif ($avgScore >= 2.00) {
            $predikat = 'Cukup';
            $badgeClass = 'warning';
        }

        // 3. Top & Bottom Dosen (Dengan Kuorum >= 3 responden)
        $scoreQuery = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->leftJoin('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->where('edom_jawaban.id_soal', $id_soal)
            ->when($id_mreg, function($q) use ($id_mreg) {
                return $q->where('edom_jawaban.id_mreg', $id_mreg);
            })
            ->when($kode_prodi, function($q) use ($kode_prodi) {
                return $q->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi);
            })
            ->when($kode_fakultas, function($q) use ($kode_fakultas) {
                return $q->where('akd_program_studi.kode_fakultas', $kode_fakultas);
            })
            ->select(
                'simpeg_pegawai.id as id_pegawai',
                'simpeg_pegawai.nama as nama',
                'simpeg_pegawai.nip as nip',
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_responden'),
                DB::raw('COUNT(*) as total_responses'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score')
            )
            ->groupBy('simpeg_pegawai.id', 'simpeg_pegawai.nama', 'simpeg_pegawai.nip');

        $allDosenScores = $scoreQuery->get();

        // Filter kuorum >= 3 (fallback jika data sedikit)
        $quorumDosen = $allDosenScores->filter(fn($d) => $d->total_responden >= 3);
        $dosenPool = $quorumDosen->count() >= 3 ? $quorumDosen : $allDosenScores;

        $topList = $dosenPool->sortByDesc('avg_score')->take(5)->map(function($r) {
            $avg = round($r->avg_score, 2);
            return [
                'id_pegawai' => $r->id_pegawai,
                'nama'       => $r->nama,
                'nip'        => $r->nip ?: '-',
                'nilai'      => $avg,
                'percent'    => round(($avg / 4) * 100, 2),
                'responden'  => (int)$r->total_responden
            ];
        })->values();

        $bottomList = $dosenPool->sortBy('avg_score')->take(5)->map(function($r) {
            $avg = round($r->avg_score, 2);
            return [
                'id_pegawai' => $r->id_pegawai,
                'nama'       => $r->nama,
                'nip'        => $r->nip ?: '-',
                'nilai'      => $avg,
                'percent'    => round(($avg / 4) * 100, 2),
                'responden'  => (int)$r->total_responden
            ];
        })->values();

        return response()->json([
            'soal_info' => $soalInfo,
            'summary' => [
                'total_answers'   => $total,
                'total_valid'     => $totalValid,
                'total_na'        => $totalNa,
                'total_mhs'       => $totalMhs,
                'avg_score'       => $avgScore,
                'percent'         => $mutuPercent,
                'predikat'        => $predikat,
                'badge'           => $badgeClass
            ],
            'pieData'    => $pieData,
            'total'      => $total,
            'topList'    => $topList,
            'bottomList' => $bottomList
        ]);
    }

    public function getSoalForReport(Request $request)
    {
        $id_mreg       = $request->input('id_mreg', Session::get('id_mreg'));
        $kode_fakultas = $request->input('kode_fakultas');
        $kode_prodi    = $request->input('kode_prodi');

        if (Session::get('is_kaprodi')) {
            $kode_prodi = Session::get('kode_program_studi');
        }

        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }

        // 1. Ambil daftar butir soal
        $soalList = DB::table('edom_soal')
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->where('edom_soal.id_mreg', $id_mreg)
            ->orderBy('edom_soal.id_soal', 'asc')
            ->select(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_soal.id_komponen_penilaian',
                DB::raw('COALESCE(edom_komponen_penilaian.nama_komponen, "Umum") as nama_komponen')
            )
            ->get();

        // 2. Agregasikan rata-rata nilai untuk setiap soal (jika with_stats diminta)
        if ($request->boolean('with_stats', true)) {
            $statsRaw = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->leftJoin('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                ->where('edom_jawaban.id_mreg', $id_mreg)
                ->when($kode_prodi, function($q) use ($kode_prodi) {
                    return $q->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi);
                })
                ->when($kode_fakultas, function($q) use ($kode_fakultas) {
                    return $q->where('akd_program_studi.kode_fakultas', $kode_fakultas);
                })
                ->select(
                    'edom_jawaban.id_soal',
                    DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
                    DB::raw('COUNT(CASE WHEN edom_jawaban.jawaban > 0 THEN 1 END) as valid_count'),
                    DB::raw('COUNT(CASE WHEN edom_jawaban.jawaban = 0 THEN 1 END) as na_count'),
                    DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score')
                )
                ->groupBy('edom_jawaban.id_soal')
                ->get();

            $soalList = $soalList->map(function($s) use ($statsRaw) {
                $stat = $statsRaw->firstWhere('id_soal', $s->id_soal);
                $avg = $stat && $stat->avg_score !== null ? round((float)$stat->avg_score, 2) : 0;
                $percent = round(($avg / 4) * 100, 2);

                $predikat = 'Kurang';
                $badge = 'danger';
                if ($avg >= 3.50) {
                    $predikat = 'Sangat Sesuai';
                    $badge = 'success';
                } elseif ($avg >= 3.00) {
                    $predikat = 'Sesuai';
                    $badge = 'info';
                } elseif ($avg >= 2.00) {
                    $predikat = 'Cukup';
                    $badge = 'warning';
                }

                return [
                    'id_soal'        => $s->id_soal,
                    'pertanyaan'     => $s->pertanyaan,
                    'id_komponen'    => $s->id_komponen_penilaian,
                    'nama_komponen'  => $s->nama_komponen,
                    'total_mhs'      => $stat ? (int)$stat->total_mhs : 0,
                    'valid_count'    => $stat ? (int)$stat->valid_count : 0,
                    'na_count'       => $stat ? (int)$stat->na_count : 0,
                    'avg_score'      => $avg,
                    'percent'        => $percent,
                    'predikat'       => $predikat,
                    'badge'          => $badge
                ];
            });
        }

        return response()->json($soalList);
    }



}
