<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    // Mengembalikan statistik mahasiswa yang sudah / belum mengisi
    public function getMahasiswaJawabanChart()
    {
        try {
            $totalStudents = DB::table('akd_mahasiswa')->count();
            $completedStudents = DB::table('edom_jawaban')->distinct('user_id')->count('user_id');
            $notCompletedStudents = max(0, $totalStudents - $completedStudents);

            return response()->json([
                'completed_students'     => $completedStudents,
                'not_completed_students' => $notCompletedStudents,
            ]);
        } catch (\Exception $e) {
            Log::error('getMahasiswaJawabanChart error: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function prodiReportView()
    {
        $fakultas = DB::table('akd_fakultas')->select('kode_fakultas', 'nama_fakultas')->get();
        $prodi = DB::table('akd_program_studi')->select('kode_program_studi', 'nama_program_studi', 'kode_fakultas')->get();
        $mregList = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->take(10)->get();

        return view('admin.report.prodi', compact('fakultas', 'prodi', 'mregList'));
    }

    /**
     * Endpoint terpadu untuk Laporan Per Program Studi
     */
    public function getProdiReportData(Request $request)
    {
        $kode_prodi = $request->input('kode_prodi', Session::get('kode_program_studi'));
        $id_mreg    = $request->input('id_mreg', Session::get('id_mreg'));

        // Fallback jika id_mreg tidak diset
        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }

        // Fallback jika kode_prodi tidak diset: ambil prodi pertama
        if (!$kode_prodi) {
            $kode_prodi = DB::table('akd_program_studi')->value('kode_program_studi');
        }

        $prodiInfo = DB::table('akd_program_studi')
            ->leftJoin('akd_fakultas', 'akd_program_studi.kode_fakultas', '=', 'akd_fakultas.kode_fakultas')
            ->where('akd_program_studi.kode_program_studi', $kode_prodi)
            ->select(
                'akd_program_studi.kode_program_studi',
                'akd_program_studi.nama_program_studi',
                'akd_fakultas.kode_fakultas',
                'akd_fakultas.nama_fakultas'
            )
            ->first();

        $mregInfo = DB::table('akd_mreg')->where('id_mreg', $id_mreg)->first();

        // 1. Query dasar jawaban pada prodi dan semester ini
        $baseQuery = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg);

        // 2. Distribusi Frekuensi Jawaban (Donut Chart)
        $pieRaw = (clone $baseQuery)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $labels = [
            0 => 'Tidak Berlaku',
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Sesuai',
            4 => 'Sangat Sesuai'
        ];

        $totalAnswers = $pieRaw->sum('count');
        $pieData = [];
        $totalValid = 0;
        $totalNa = 0;

        foreach ($labels as $key => $lbl) {
            $found = $pieRaw->firstWhere('jawaban', $key);
            $cnt = $found ? (int)$found->count : 0;
            $pct = $totalAnswers > 0 ? round(($cnt / $totalAnswers) * 100, 2) : 0;

            if ($key == 0) {
                $totalNa += $cnt;
            } else {
                $totalValid += $cnt;
            }

            $pieData[] = [
                'jawaban'    => $key,
                'name'       => $lbl,
                'count'      => $cnt,
                'percentage' => $pct
            ];
        }

        // 3. Ringkasan Metrik KPI Prodi (Eksklusi 0 = Tidak Berlaku)
        $stats = (clone $baseQuery)->select(
            DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
            DB::raw('COUNT(DISTINCT edom_jawaban.id_kelas) as total_kelas_terjawab'),
            DB::raw('COUNT(DISTINCT akd_penawaran_matakuliah.kode_dosen) as total_dosen_terevaluasi'),
            DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as overall_avg')
        )->first();

        $overallAvg     = $stats && $stats->overall_avg !== null ? round((float)$stats->overall_avg, 2) : 0;
        $overallPercent = round(($overallAvg / 4) * 100, 2);
        $totalMhs       = $stats ? (int)$stats->total_mhs : 0;

        // Predikat Mutu Prodi
        $predikatProdi = 'Kurang';
        $badgeClassProdi = 'danger';
        if ($overallAvg >= 3.50) {
            $predikatProdi = 'Sangat Memuaskan';
            $badgeClassProdi = 'success';
        } elseif ($overallAvg >= 3.00) {
            $predikatProdi = 'Memuaskan';
            $badgeClassProdi = 'info';
        } elseif ($overallAvg >= 2.00) {
            $predikatProdi = 'Cukup';
            $badgeClassProdi = 'warning';
        }

        // 4. Kinerja Seluruh Dosen di Prodi (Tab 1: Kinerja Dosen)
        // Ambil penawaran di prodi ini, lalu agregasikan jawaban
        $dosenRows = DB::table('akd_penawaran_matakuliah')
            ->join('akd_kelas_kuliah', 'akd_penawaran_matakuliah.id_tawar', '=', 'akd_kelas_kuliah.id_tawar')
            ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->leftJoin('edom_jawaban', function($join) use ($id_mreg) {
                $join->on('akd_kelas_kuliah.id_kelas', '=', 'edom_jawaban.id_kelas')
                     ->where('edom_jawaban.id_mreg', '=', $id_mreg);
            })
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->when($mregInfo, function($q) use ($mregInfo) {
                return $q->where('akd_penawaran_matakuliah.tahun', $mregInfo->tahun)
                         ->where('akd_penawaran_matakuliah.semester', $mregInfo->semester);
            })
            ->select(
                'simpeg_pegawai.id as id_pegawai',
                'simpeg_pegawai.nama as nama_dosen',
                'simpeg_pegawai.nip as nip_dosen',
                DB::raw('COUNT(DISTINCT akd_kelas_kuliah.id_kelas) as total_kelas'),
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_responden'),
                DB::raw('COUNT(edom_jawaban.jawaban) as total_jawaban'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score')
            )
            ->groupBy('simpeg_pegawai.id', 'simpeg_pegawai.nama', 'simpeg_pegawai.nip')
            ->orderBy('avg_score', 'desc')
            ->get();

        $dosenList = $dosenRows->map(function($d) {
            $avg = $d->avg_score !== null ? round((float)$d->avg_score, 2) : 0;
            $percent = round(($avg / 4) * 100, 2);
            $predikat = 'Belum Ada Data';
            $badge = 'secondary';
            if ($d->total_responden > 0) {
                if ($avg >= 3.50) {
                    $predikat = 'Sangat Memuaskan';
                    $badge = 'success';
                } elseif ($avg >= 3.00) {
                    $predikat = 'Memuaskan';
                    $badge = 'info';
                } elseif ($avg >= 2.00) {
                    $predikat = 'Cukup';
                    $badge = 'warning';
                } else {
                    $predikat = 'Kurang';
                    $badge = 'danger';
                }
            }

            return [
                'id_pegawai'      => $d->id_pegawai,
                'nama'            => $d->nama_dosen,
                'nip'             => $d->nip_dosen ?: '-',
                'total_kelas'     => (int)$d->total_kelas,
                'total_responden' => (int)$d->total_responden,
                'total_jawaban'   => (int)$d->total_jawaban,
                'avg_score'       => $avg,
                'percent'         => $percent,
                'predikat'        => $predikat,
                'badge'           => $badge,
                'is_quorum'       => $d->total_responden >= 3
            ];
        });

        // 5. Rekapitulasi Kelas & Mata Kuliah (Tab 2: Kelas & Matkul)
        $kelasRows = DB::table('akd_kelas_kuliah')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->leftJoin('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->leftJoin('edom_jawaban', function($join) use ($id_mreg) {
                $join->on('akd_kelas_kuliah.id_kelas', '=', 'edom_jawaban.id_kelas')
                     ->where('edom_jawaban.id_mreg', '=', $id_mreg);
            })
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->when($mregInfo, function($q) use ($mregInfo) {
                return $q->where('akd_penawaran_matakuliah.tahun', $mregInfo->tahun)
                         ->where('akd_penawaran_matakuliah.semester', $mregInfo->semester);
            })
            ->select(
                'akd_kelas_kuliah.id_kelas',
                'akd_kelas_kuliah.nama_kelas',
                'akd_matakuliah.kode_matakuliah',
                'akd_matakuliah.nama_matakuliah',
                'akd_penawaran_matakuliah.smt_matakuliah as semester',
                'simpeg_pegawai.id as id_dosen',
                'simpeg_pegawai.nama as nama_dosen',
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
                DB::raw('COUNT(edom_jawaban.jawaban) as total_jawaban'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score')
            )
            ->groupBy(
                'akd_kelas_kuliah.id_kelas',
                'akd_kelas_kuliah.nama_kelas',
                'akd_matakuliah.kode_matakuliah',
                'akd_matakuliah.nama_matakuliah',
                'akd_penawaran_matakuliah.smt_matakuliah',
                'simpeg_pegawai.id',
                'simpeg_pegawai.nama'
            )
            ->orderBy('akd_matakuliah.nama_matakuliah')
            ->get();

        $kelasList = $kelasRows->map(function($k) {
            $avg = $k->avg_score !== null ? round((float)$k->avg_score, 2) : 0;
            $percent = round(($avg / 4) * 100, 2);
            $predikat = 'Belum Ada Data';
            $badge = 'secondary';
            if ($k->total_mhs > 0) {
                if ($avg >= 3.50) {
                    $predikat = 'Sangat Memuaskan';
                    $badge = 'success';
                } elseif ($avg >= 3.00) {
                    $predikat = 'Memuaskan';
                    $badge = 'info';
                } elseif ($avg >= 2.00) {
                    $predikat = 'Cukup';
                    $badge = 'warning';
                } else {
                    $predikat = 'Kurang';
                    $badge = 'danger';
                }
            }

            return [
                'id_kelas'        => $k->id_kelas,
                'nama_kelas'      => $k->nama_kelas,
                'kode_matakuliah' => $k->kode_matakuliah,
                'nama_matakuliah' => $k->nama_matakuliah,
                'semester'        => $k->semester,
                'id_dosen'        => $k->id_dosen,
                'nama_dosen'      => $k->nama_dosen ?: 'Belum Ditentukan',
                'total_mhs'       => (int)$k->total_mhs,
                'total_jawaban'   => (int)$k->total_jawaban,
                'avg_score'       => $avg,
                'percent'         => $percent,
                'predikat'        => $predikat,
                'badge'           => $badge,
                'is_quorum'       => $k->total_mhs >= 3
            ];
        });

        // 6. Skor Per Komponen Penilaian (Tab 3: Komponen Penilaian)
        $komponenStats = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('edom_soal', 'edom_jawaban.id_soal', '=', 'edom_soal.id_soal')
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select(
                'edom_komponen_penilaian.id_komponen_penilaian',
                DB::raw('COALESCE(edom_komponen_penilaian.nama_komponen, "Komponen Umum") as nama_komponen'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score'),
                DB::raw('COUNT(CASE WHEN edom_jawaban.jawaban > 0 THEN 1 END) as valid_responses')
            )
            ->groupBy('edom_komponen_penilaian.id_komponen_penilaian', 'edom_komponen_penilaian.nama_komponen')
            ->get()
            ->map(function($c) {
                $avg = $c->avg_score !== null ? round((float)$c->avg_score, 2) : 0;
                return [
                    'id_komponen'   => $c->id_komponen_penilaian,
                    'nama_komponen' => $c->nama_komponen,
                    'avg_score'     => $avg,
                    'percent'       => round(($avg / 4) * 100, 2),
                    'responses'     => (int)$c->valid_responses
                ];
            });

        // 7. Monev Mutu Prodi (Tab 4: Monev)
        $validDosen = $dosenList->filter(fn($d) => $d['is_quorum']);
        $bestDosen = $validDosen->where('avg_score', '>=', 3.50)->values();
        $needSupportDosen = $validDosen->where('avg_score', '<', 3.00)->values();
        $lowQuorumKelas = $kelasList->filter(fn($k) => !$k['is_quorum'])->values();

        return response()->json([
            'prodi_info' => [
                'kode_program_studi' => $prodiInfo ? $prodiInfo->kode_program_studi : $kode_prodi,
                'nama_program_studi' => $prodiInfo ? $prodiInfo->nama_program_studi : 'Program Studi',
                'kode_fakultas'      => $prodiInfo ? $prodiInfo->kode_fakultas : '-',
                'nama_fakultas'      => $prodiInfo ? ($prodiInfo->nama_fakultas ?: 'Fakultas') : 'Fakultas',
                'tahun_akademik'     => $mregInfo ? ($mregInfo->tahun . ' / ' . ($mregInfo->semester == '1' ? 'Ganjil' : 'Genap')) : '-'
            ],
            'summary_kpi' => [
                'overall_avg'     => $overallAvg,
                'overall_percent' => $overallPercent,
                'predikat'        => $predikatProdi,
                'badge'           => $badgeClassProdi,
                'total_mhs'       => $totalMhs,
                'total_dosen'     => $dosenRows->count(),
                'total_kelas'     => $kelasList->count(),
                'total_answers'   => $totalAnswers,
                'total_valid'     => $totalValid,
                'total_na'        => $totalNa,
            ],
            'distribusi_skala' => $pieData,
            'dosen_list'       => $dosenList,
            'kelas_list'       => $kelasList,
            'komponen_scores'  => $komponenStats,
            'monev_summary'    => [
                'best_dosen'         => $bestDosen,
                'need_support_dosen' => $needSupportDosen,
                'low_quorum_kelas'   => $lowQuorumKelas
            ]
        ]);
    }

    public function reportProdiUniversal(Request $request)
    {
        $kode_prodi = $request->input('kode_prodi', Session::get('kode_program_studi'));
        $id_mreg    = $request->input('id_mreg', Session::get('id_mreg'));

        if (!$kode_prodi) {
            $kode_prodi = DB::table('akd_program_studi')->value('kode_program_studi');
        }
        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }

        $labels = [0=>'Tidak Berlaku',1=>'Sangat Tidak Sesuai',2=>'Tidak Sesuai',3=>'Sesuai',4=>'Sangat Sesuai'];

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $total = $rows->sum('count');
        $pieData = [];
        foreach ($labels as $k => $v) {
            $r = $rows->firstWhere('jawaban', $k);
            $cnt = $r ? $r->count : 0;
            $pieData[] = ['name'=>$v,'value'=>$cnt,'percentage'=> $total ? round(($cnt/$total)*100,2) : 0];
        }

        return response()->json(['pieData'=>$pieData,'total'=>$total]);
    }

    public function reportProdiPerDosen(Request $request)
    {
        $kode_prodi = $request->input('kode_prodi', Session::get('kode_program_studi'));
        $id_mreg    = $request->input('id_mreg', Session::get('id_mreg'));

        if (!$kode_prodi) {
            $kode_prodi = DB::table('akd_program_studi')->value('kode_program_studi');
        }
        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }

        try {
            $rows = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
                ->join('simpeg_pegawai','akd_penawaran_matakuliah.kode_dosen','=','simpeg_pegawai.id')
                ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
                ->where('edom_jawaban.id_mreg', $id_mreg)
                ->select(
                    'simpeg_pegawai.id as id_pegawai',
                    'simpeg_pegawai.nama as nama',
                    'simpeg_pegawai.nip as nip',
                    DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score'),
                    DB::raw('COUNT(*) as total_responses')
                )
                ->groupBy('simpeg_pegawai.id','simpeg_pegawai.nama','simpeg_pegawai.nip')
                ->orderBy('avg_score','desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('reportProdiPerDosen query error: '.$e->getMessage());
            return response()->json(['list'=>[], 'message'=>'Query error: '.$e->getMessage()]);
        }

        $list = $rows->map(function($r){
            $avg = $r->avg_score !== null ? round((float)$r->avg_score, 2) : 0;
            return [
                'id_pegawai' => $r->id_pegawai,
                'nama'       => $r->nama,
                'nip'        => $r->nip,
                'avg'        => $avg,
                'percent'    => round(($avg/4)*100,2),
                'responses'  => (int)$r->total_responses
            ];
        })->values();

        return response()->json(['list'=>$list]);
    }

    public function reportProdiPerKelas(Request $request)
    {
        $id_dosen   = $request->input('id_dosen');
        $kode_prodi = $request->input('kode_prodi', Session::get('kode_program_studi'));
        $id_mreg    = $request->input('id_mreg', Session::get('id_mreg'));

        if (!$kode_prodi) {
            $kode_prodi = DB::table('akd_program_studi')->value('kode_program_studi');
        }
        if (!$id_mreg) {
            $id_mreg = DB::table('akd_mreg')->orderBy('id_mreg', 'desc')->value('id_mreg');
        }
        if (!$id_dosen) {
            return response()->json(['kelas'=>[], 'message'=>'id_dosen required'], 400);
        }

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah','akd_penawaran_matakuliah.id_matakuliah','=','akd_matakuliah.id_matakuliah')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_dosen)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select(
                'akd_kelas_kuliah.id_kelas',
                'akd_kelas_kuliah.nama_kelas',
                'akd_matakuliah.nama_matakuliah',
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score'),
                DB::raw('COUNT(*) as total_responses')
            )
            ->groupBy('akd_kelas_kuliah.id_kelas','akd_kelas_kuliah.nama_kelas','akd_matakuliah.nama_matakuliah')
            ->orderBy('avg_score','desc')
            ->get();

        $kelas = $rows->map(function($r){
            $avg = $r->avg_score !== null ? round((float)$r->avg_score, 2) : 0;
            return [
                'id_kelas'        => $r->id_kelas,
                'nama_kelas'      => $r->nama_kelas,
                'nama_matakuliah' => $r->nama_matakuliah,
                'avg'             => $avg,
                'percent'         => round(($avg/4)*100,2),
                'responses'       => (int)$r->total_responses
            ];
        })->values();

        return response()->json(['kelas'=>$kelas]);
    }
}
