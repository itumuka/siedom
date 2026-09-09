<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class JawabanController extends Controller
{

    public function getDataMahasiswaSudahMengisi()
    {
        try {
            $query = DB::table('akd_mahasiswa')
                ->select(
                    'akd_mahasiswa.id_mhs',
                    'akd_mahasiswa.nim',
                    'akd_mahasiswa.nama_mahasiswa',
                    'akd_mahasiswa.tahun_angkatan',
                    'nama_program_studi',
                    'akd_mahasiswa.kode_program_studi',
                    DB::raw("CONCAT_WS(' ', akd_mahasiswa.tahun_angkatan, IF(akd_mahasiswa.semester = '1', 'Ganjil', 'Genap')) AS tahun_ajaran"),
                    DB::raw('COUNT(edom_jawaban.jawaban) as total_jawaban'),
                    DB::raw('COUNT(DISTINCT edom_jawaban.id_kelas) as total_kelas')
                )
                ->join('edom_jawaban', function ($join) {
                    $join->on('akd_mahasiswa.id_mhs', '=', 'edom_jawaban.user_id')
                         ->where('edom_jawaban.id_mreg', Session::get('id_mreg'));
                })
                ->join('akd_program_studi', 'akd_mahasiswa.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                ->groupBy(
                    'akd_mahasiswa.id_mhs',
                    'akd_mahasiswa.nim',
                    'akd_mahasiswa.nama_mahasiswa',
                    'akd_mahasiswa.tahun_angkatan',
                    'akd_mahasiswa.kode_program_studi',
                    'akd_mahasiswa.semester',
                    'akd_program_studi.nama_program_studi'
                );
    
            $totalRecords = $query->count();
            $data = $query->get();
    
            return response()->json([
                'draw' => intval(request()->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDataMahasiswaBelumMengisi()
    {
        try {
            $query = DB::table('akd_mahasiswa')
                ->select(
                    'akd_mahasiswa.id_mhs',
                    'akd_mahasiswa.nim',
                    'akd_mahasiswa.nama_mahasiswa',
                    'akd_mahasiswa.tahun_angkatan',
                    'akd_program_studi.nama_program_studi',
                    DB::raw("CONCAT_WS(' ', akd_mahasiswa.tahun_angkatan, IF(akd_mahasiswa.semester = '1', 'Ganjil', 'Genap')) AS tahun_ajaran")
                )
                ->join('akd_program_studi', 'akd_mahasiswa.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                ->leftJoin('edom_jawaban', function ($join) {
                    $join->on('akd_mahasiswa.id_mhs', '=', 'edom_jawaban.user_id')
                         ->where('edom_jawaban.id_mreg', Session::get('id_mreg'));
                })
                ->where('akd_mahasiswa.status_mhs', 'A')
                ->whereNull('edom_jawaban.user_id')
                ->groupBy(
                    'akd_mahasiswa.id_mhs',
                    'akd_mahasiswa.nim',
                    'akd_mahasiswa.nama_mahasiswa',
                    'akd_mahasiswa.tahun_angkatan',
                    'akd_mahasiswa.kode_program_studi',
                    'akd_mahasiswa.semester',
                    'akd_program_studi.nama_program_studi'
                );

            $totalRecords = $query->count();
            $data = $query->get();

            return response()->json([
                'draw' => intval(request()->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAverageScores($id_kelas)
    {
        try {
            // Query to calculate the average score for all answers for a specific subject
            $query = DB::table('edom_jawaban')
                ->select(
                    'akd_matakuliah.nama_matakuliah', // Subject name
                    DB::raw('AVG(edom_jawaban.jawaban) as avg_score') // Average score calculation
                )
                ->join('edom_soal', 'edom_jawaban.id_soal', '=', 'edom_soal.id_soal') // Join to get subject info
                ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
                ->join('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
                ->join('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi') // Join to get the matakuliah name
                ->where('edom_jawaban.id_kelas', $id_kelas) 
                ->groupBy('akd_matakuliah.nama_matakuliah') 
                ->get();

            return response()->json([
                'data' => $query
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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

    public function getDetailMahasiswaJawaban($id_mhs)
    {
        try {
            $id_mreg = Session::get('id_mreg');
            
            $query = DB::table('edom_jawaban')
                ->select(
                    'edom_jawaban.id_kelas',
                    'akd_matakuliah.nama_matakuliah',
                    DB::raw('COUNT(edom_jawaban.jawaban) as total_jawaban')
                )
                ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
                ->where('edom_jawaban.user_id', $id_mhs)
                ->where('edom_jawaban.id_mreg', $id_mreg)
                ->groupBy('edom_jawaban.id_kelas', 'akd_matakuliah.nama_matakuliah')
                ->get();
    
            $totalMatkul = DB::table('edom_jawaban')
                ->join('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
                ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
                ->where('edom_jawaban.user_id', $id_mhs)
                ->where('edom_jawaban.id_mreg', $id_mreg)
                ->distinct()
                ->count('akd_penawaran_matakuliah.id_matakuliah');
    
            $student = DB::table('akd_mahasiswa')
                ->select('nama_mahasiswa',
                'nim',
                'akd_mahasiswa.kode_program_studi', 'nama_program_studi')
                ->join('akd_program_studi', 'akd_mahasiswa.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
                ->where('id_mhs', $id_mhs)
                ->first();
    
            return view('admin.jawaban.detail_mahasiswa_jawaban', [
                'mahasiswa' => [
                    'id_mhs' => $id_mhs,
                    'nim' => $student->nim,
                    'nama_program_studi' => $student->nama_program_studi,
                    'nama' => $student->nama_mahasiswa ?? 'Unknown'
                ],
                'total_matkul' => $totalMatkul,
                'detail_jawaban' => $query
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }



    
    

    public function presensimakul(Request $request)
    {

        $check_herregistrasi = collect(DB::select("SELECT * 
        FROM akd_heregistrasi JOIN akd_krs ON akd_heregistrasi.id_heregistrasi = akd_krs.id_heregistrasi 
        WHERE akd_heregistrasi.nim ='" . $request->nim . "' 
        AND akd_heregistrasi.tahun = '" . $request->tahun . "' 
        AND akd_heregistrasi.semester='" . $request->semester . "'"))->first();

        $id_her = isset($check_herregistrasi->id_heregistrasi) ? $check_herregistrasi->id_heregistrasi : 0;
        $nim = isset($check_herregistrasi->nim) ? $check_herregistrasi->nim : 0;

        // untuk mengaktifkan cegatan pembayaran UTS
        $querybyr1 = DB::select("SELECT * FROM (SELECT nim,(SELECT SUM(bayar) AS jum FROM keu_bayar aaa WHERE aaa.id_tagihan=keu_tagihan.id_tagihan) AS bayar FROM keu_tagihan 
        WHERE nim='" . $request->nim . "' AND tahun='" . $request->tahun . "' AND semester='" . $request->semester . "' AND nama_biaya LIKE '%SPP VARIABLE%') AS tbl1 WHERE bayar IS NOT NULL");
        $querybyr2 = DB::select("SELECT nim FROM akd_dispensasi WHERE nim='" . $request->nim . "' AND tahun='" . $request->tahun . "' AND semester='" . $request->semester . "' AND jenis='UTS'");
        $querybyr3 = DB::select("SELECT nim FROM akd_mahasiswa WHERE nim='" . $request->nim . "' AND beasiswa='1'");
        $cekbyr1 = collect($querybyr1)->count();
        $cekbyr2 = collect($querybyr2)->count();
        $cekbyr3 = collect($querybyr3)->count();
        $cekbbyran = 1;
        if ($cekbyr1 == 0 && $cekbyr2 == 0 && $cekbyr3 == 0) {
            $cekbbyran = 0;
        }


        $statusuas = DB::select("SELECT nim FROM keu_tagihan WHERE nim='" . $request->nim . "' AND tahun='" . $request->tahun . "' AND semester='" . $request->semester . "' AND ( nama_biaya LIKE 'SPP VARIABLE%' OR nama_biaya LIKE '%SPP Tetap Kelas Pegawai%' OR nama_biaya LIKE '%PEMBIAYAAN SPP BPE%' ) AND status='1'");
        $cekstatusuas1 = collect($statusuas)->count();
        $querybyruas2 = DB::select("SELECT nim FROM akd_dispensasi WHERE nim='" . $request->nim . "' AND tahun='" . $request->tahun . "' AND semester='" . $request->semester . "' AND jenis='UAS'");
        $cekbyrnuas = 1;
        if ($cekstatusuas1 == 0 && $querybyruas2 == 0 && $cekbyr3 == 0) {
            $cekbyrnuas = 0;
        }

        $presensimakul = DB::select("SELECT *,IF(((NOW())>= CONCAT_WS(' ',tglbrt,jam_mulaibrt)) 
        AND ((NOW())<= CONCAT_WS(' ',tglbrt,jam_selesaibrt)), 1,0) AS button_in,
        CONCAT_WS(' s/d ', TIME_FORMAT(jam_mulaibrt, '%H:%i'), TIME_FORMAT(jam_selesaibrt, '%H:%i')) AS jam, CONCAT_WS(' s/d ', TIME_FORMAT(tbl1.jam_mulai, '%H:%i'), TIME_FORMAT(tbl1.jam_selesai, '%H:%i')) AS jam_semula,
        CASE DAYOFWEEK(tglbrt)
            WHEN 1 THEN 'Minggu'
            WHEN 2 THEN 'Senin'
            WHEN 3 THEN 'Selasa'
            WHEN 4 THEN 'Rabu'
            WHEN 5 THEN 'Kamis'
            WHEN 6 THEN 'Jumat'
            WHEN 7 THEN 'Sabtu'
        END AS hari,(SELECT id FROM akd_presensi_mhs WHERE id_kelas=tbl1.id_kelas AND pertemuan=tbl1.pertemuan_ke AND hadir LIKE '%$nim%') AS kehadiran FROM (SELECT akd_kelas_kuliah.id_kelas,kode_matakuliah, nama_matakuliah,akd_kelas_kuliah.jam_mulai,akd_kelas_kuliah.jam_selesai, 
        akd_penawaran_matakuliah.sks_matakuliah AS sks,
         akd_kelas_kuliah.hari AS hari_semula,  
        IF(akd_penawaran_matakuliah.smt_matakuliah = '1', 'Ganjil', 'Genap' ) AS semester, nama_kelas,
        (SELECT pertemuan_ke FROM akd_berita_acara a WHERE a.id_kelas=akd_detail_krs.id_kelas AND CONCAT_WS(' ',tgl,jam_selesai)>=(NOW()) ORDER BY tgl,jam_mulai LIMIT 1) AS pertemuan_ke,
        (SELECT tgl FROM akd_berita_acara a WHERE a.id_kelas=akd_detail_krs.id_kelas AND CONCAT_WS(' ',tgl,jam_selesai)>=(NOW()) ORDER BY tgl,jam_mulai LIMIT 1) AS tglbrt,
        (SELECT jam_mulai FROM akd_berita_acara a WHERE a.id_kelas=akd_detail_krs.id_kelas AND CONCAT_WS(' ',tgl,jam_selesai)>=(NOW()) ORDER BY tgl,jam_mulai LIMIT 1) AS jam_mulaibrt,
        (SELECT jam_selesai FROM akd_berita_acara a WHERE a.id_kelas=akd_detail_krs.id_kelas AND CONCAT_WS(' ',tgl,jam_selesai)>=(NOW()) ORDER BY tgl,jam_mulai LIMIT 1) AS jam_selesaibrt,
        CONCAT_WS(' ', gelar_depan, simpeg_pegawai.nama,gelar_belakang) AS dosen, kode_ruang,(NOW()) as cektglwaktu,'" . $cekbbyran . "' as cekuts,'" . $cekbyrnuas . "' as cekuas FROM akd_detail_krs 
        JOIN akd_krs ON akd_krs.id_krs = akd_detail_krs.id_krs
        JOIN akd_heregistrasi ON akd_heregistrasi.id_heregistrasi = akd_krs.id_heregistrasi
        JOIN akd_kelas_kuliah ON akd_detail_krs.id_kelas = akd_kelas_kuliah.id_kelas
        JOIN akd_penawaran_matakuliah ON akd_kelas_kuliah.id_tawar = akd_penawaran_matakuliah.id_tawar
        JOIN akd_matakuliah ON akd_matakuliah.id_matakuliah = akd_penawaran_matakuliah.id_matakuliah
        LEFT JOIN simpeg_pegawai ON simpeg_pegawai.id = akd_penawaran_matakuliah.kode_dosen
        WHERE akd_krs.id_heregistrasi='" . $id_her . "' AND akd_heregistrasi.krs='1'
        ORDER BY akd_kelas_kuliah.hari DESC) AS tbl1");
        return $presensimakul;
    }

    
    public function overviewSoal($id_kelas)
    {
        return view('admin.kelas.overview_soal', ['id_kelas' => $id_kelas]);
    }

    public function getAllSoalData($id_kelas)
    {
        $id_mreg = Session::get('id_mreg');

        // 1. Ambil informasi master kelas, mata kuliah, dosen, dan prodi
        $kelasInfo = DB::table('akd_kelas_kuliah')
            ->join('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah', 'akd_penawaran_matakuliah.id_matakuliah', '=', 'akd_matakuliah.id_matakuliah')
            ->leftJoin('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->leftJoin('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->where('akd_kelas_kuliah.id_kelas', $id_kelas)
            ->select(
                'akd_kelas_kuliah.id_kelas',
                'akd_kelas_kuliah.nama_kelas',
                'akd_matakuliah.nama_matakuliah',
                'akd_matakuliah.kode_matakuliah',
                'akd_penawaran_matakuliah.smt_matakuliah as semester',
                'akd_penawaran_matakuliah.tahun',
                'akd_program_studi.nama_program_studi',
                'simpeg_pegawai.nama as nama_dosen',
                'simpeg_pegawai.nip as nip_dosen'
            )
            ->first();

        // Jika session id_mreg kosong, cari mreg yang cocok dari tahun & semester penawaran atau dari jawaban
        if (!$id_mreg && $kelasInfo) {
            $mregFound = DB::table('akd_mreg')
                ->where('tahun', $kelasInfo->tahun)
                ->where('semester', $kelasInfo->semester)
                ->value('id_mreg');
            if ($mregFound) {
                $id_mreg = $mregFound;
            }
        }
        if (!$id_mreg) {
            $id_mreg = DB::table('edom_jawaban')->where('id_kelas', $id_kelas)->value('id_mreg');
        }

        // 2. Query dasar jawaban untuk kelas ini
        $jawabanQuery = DB::table('edom_jawaban')
            ->where('edom_jawaban.id_kelas', $id_kelas);
        if ($id_mreg) {
            $jawabanQuery->where('edom_jawaban.id_mreg', $id_mreg);
        }

        // 3. Ambil daftar butir soal yang diujikan (dari edom_soal join edom_komponen_penilaian)
        $soalListRaw = DB::table('edom_soal')
            ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
            ->when($id_mreg, function($q) use ($id_mreg) {
                return $q->where('edom_soal.id_mreg', $id_mreg);
            })
            ->select(
                'edom_soal.id_soal',
                'edom_soal.pertanyaan',
                'edom_soal.id_komponen_penilaian',
                'edom_komponen_penilaian.nama_komponen'
            )
            ->orderBy('edom_soal.id_soal')
            ->get();

        // Fallback jika tidak ada soal ber-id_mreg sama: ambil dari soal yang ada di edom_jawaban
        if ($soalListRaw->isEmpty()) {
            $soalIdsInJawaban = (clone $jawabanQuery)->distinct('id_soal')->pluck('id_soal');
            $soalListRaw = DB::table('edom_soal')
                ->leftJoin('edom_komponen_penilaian', 'edom_soal.id_komponen_penilaian', '=', 'edom_komponen_penilaian.id_komponen_penilaian')
                ->whereIn('edom_soal.id_soal', $soalIdsInJawaban)
                ->select(
                    'edom_soal.id_soal',
                    'edom_soal.pertanyaan',
                    'edom_soal.id_komponen_penilaian',
                    'edom_komponen_penilaian.nama_komponen'
                )
                ->orderBy('edom_soal.id_soal')
                ->get();
        }

        // 4. Agregasi jawaban per soal dan per skala
        $distRaw = (clone $jawabanQuery)
            ->select('id_soal', 'jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('id_soal', 'jawaban')
            ->get();

        // 5. Hitung rata-rata dan total per soal
        $labels = [
            0 => 'Tidak Berlaku',
            1 => 'Sangat Tidak Sesuai',
            2 => 'Tidak Sesuai',
            3 => 'Sesuai',
            4 => 'Sangat Sesuai'
        ];

        $soalList = [];
        $rawCompatible = [];
        $totalValidSemua = 0;
        $totalNaSemua = 0;

        foreach ($soalListRaw as $soal) {
            $soalDistRaw = $distRaw->where('id_soal', $soal->id_soal);
            $totalCountSoal = $soalDistRaw->sum('count');

            $dist = [];
            $sumScore = 0;
            $countValid = 0;
            $countNa = 0;

            foreach ($labels as $key => $lbl) {
                $found = $soalDistRaw->firstWhere('jawaban', $key);
                $cnt = $found ? (int)$found->count : 0;
                $pct = $totalCountSoal > 0 ? round(($cnt / $totalCountSoal) * 100, 2) : 0;

                if ($key == 0) {
                    $countNa += $cnt;
                } else {
                    $countValid += $cnt;
                    $sumScore += ($key * $cnt);
                }

                $dist[] = [
                    'jawaban'    => $key,
                    'name'       => $lbl,
                    'count'      => $cnt,
                    'percentage' => $pct
                ];

                if ($cnt > 0) {
                    $rawCompatible[] = (object)[
                        'id_soal'         => $soal->id_soal,
                        'pertanyaan'      => $soal->pertanyaan,
                        'jawaban'         => $key,
                        'count'           => $cnt,
                        'nama_matakuliah' => $kelasInfo ? $kelasInfo->nama_matakuliah : '-',
                        'kode_matakuliah' => $kelasInfo ? $kelasInfo->kode_matakuliah : '-',
                        'nama_dosen'      => $kelasInfo ? $kelasInfo->nama_dosen : '-'
                    ];
                }
            }

            $totalValidSemua += $countValid;
            $totalNaSemua += $countNa;

            // Rata-rata mengecualikan 0 = Tidak Berlaku
            $avgScore = $countValid > 0 ? round($sumScore / $countValid, 2) : 0;
            $percentMutu = round(($avgScore / 4) * 100, 2);

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

            $soalList[] = [
                'id_soal'        => $soal->id_soal,
                'pertanyaan'     => $soal->pertanyaan,
                'id_komponen'    => $soal->id_komponen_penilaian,
                'nama_komponen'  => $soal->nama_komponen ?: 'Umum / Lainnya',
                'valid_count'    => $countValid,
                'na_count'       => $countNa,
                'total_count'    => $totalCountSoal,
                'avg_score'      => $avgScore,
                'percent'        => $percentMutu,
                'predikat'       => $predikat,
                'badge_class'    => $badgeClass,
                'distribution'   => $dist
            ];
        }

        // 6. Ringkasan Keseluruhan Kelas
        $totalMhs = (clone $jawabanQuery)->distinct('user_id')->count('user_id');
        $overallStats = (clone $jawabanQuery)->select(
            DB::raw('AVG(CASE WHEN jawaban > 0 THEN jawaban ELSE NULL END) as overall_avg'),
            DB::raw('COUNT(*) as total_answers')
        )->first();

        $overallAvg = $overallStats && $overallStats->overall_avg !== null ? round((float)$overallStats->overall_avg, 2) : 0;
        $overallPercent = round(($overallAvg / 4) * 100, 2);

        // Cari soal dengan skor tertinggi dan terendah
        $sortedSoal = collect($soalList)->filter(fn($s) => $s['valid_count'] > 0)->sortByDesc('avg_score')->values();
        $highestSoal = $sortedSoal->first();
        $lowestSoal  = $sortedSoal->last();

        return response()->json([
            'kelas_info' => [
                'id_kelas'           => $kelasInfo ? $kelasInfo->id_kelas : $id_kelas,
                'nama_kelas'         => $kelasInfo ? $kelasInfo->nama_kelas : '-',
                'nama_matakuliah'    => $kelasInfo ? $kelasInfo->nama_matakuliah : '-',
                'kode_matakuliah'    => $kelasInfo ? $kelasInfo->kode_matakuliah : '-',
                'nama_program_studi' => $kelasInfo ? $kelasInfo->nama_program_studi : '-',
                'semester'           => $kelasInfo ? $kelasInfo->semester : '-',
                'nama_dosen'         => $kelasInfo ? ($kelasInfo->nama_dosen ?: 'Belum Ditentukan') : '-',
                'nip_dosen'          => $kelasInfo ? ($kelasInfo->nip_dosen ?: '-') : '-'
            ],
            'class_summary' => [
                'total_mhs'       => $totalMhs,
                'total_soal'      => count($soalList),
                'overall_avg'     => $overallAvg,
                'overall_percent' => $overallPercent,
                'total_valid'     => $totalValidSemua,
                'total_na'        => $totalNaSemua,
                'highest_soal'    => $highestSoal,
                'lowest_soal'     => $lowestSoal
            ],
            'soal_list' => $soalList,
            // Backward-compatible keys
            'data'            => $rawCompatible,
            'nama_matakuliah' => $kelasInfo ? $kelasInfo->nama_matakuliah : '-',
            'kode_matakuliah' => $kelasInfo ? $kelasInfo->kode_matakuliah : '-',
            'nama_dosen'      => $kelasInfo ? ($kelasInfo->nama_dosen ?: '-') : '-'
        ]);
    }



    public function getGeneralDashboard(Request $request)
    {
        $type = $request->input('type', 'universal');
        $fakultas = $request->input('fakultas');
        $prodi = $request->input('prodi');
        $id_mreg = Session::get('id_mreg');

        if (!$id_mreg) {
            return response()->json([
                'session_active' => false,
                'message' => 'Tahun akademik belum dipilih di session.',
                'pieData' => [],
                'total' => 0,
                'total_valid' => 0,
                'total_na' => 0,
                'total_mhs' => 0,
                'total_dosen' => 0,
                'overall_avg' => 0,
                'overall_percent' => 0,
                'topList' => [],
                'bottomList' => []
            ]);
        }

        // Query dasar menggunakan LEFT JOIN agar data jawaban sah mahasiswa tidak hilang jika data master dosen/prodi belum lengkap
        $baseQuery = DB::table('edom_jawaban')
            ->leftJoin('akd_kelas_kuliah', 'edom_jawaban.id_kelas', '=', 'akd_kelas_kuliah.id_kelas')
            ->leftJoin('akd_penawaran_matakuliah', 'akd_kelas_kuliah.id_tawar', '=', 'akd_penawaran_matakuliah.id_tawar')
            ->leftJoin('akd_program_studi', 'akd_penawaran_matakuliah.kode_program_studi', '=', 'akd_program_studi.kode_program_studi')
            ->leftJoin('simpeg_pegawai', 'akd_penawaran_matakuliah.kode_dosen', '=', 'simpeg_pegawai.id')
            ->where('edom_jawaban.id_mreg', $id_mreg);

        // Filter fakultas / prodi
        if ($type == 'fakultas' && $fakultas) {
            $baseQuery->where('akd_program_studi.kode_fakultas', $fakultas);
        }
        if ($type == 'prodi' && $prodi) {
            $baseQuery->where('akd_program_studi.kode_program_studi', $prodi);
        }

        // Pie chart data (distribusi jawaban)
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
                'name' => $label,
                'key' => $key,
                'value' => $count,
                'percentage' => $percentage
            ];
        }

        // Metrik Eksekutif: Partisipasi Mahasiswa, Dosen Tervalidasi, dan Skor Keseluruhan
        $summaryStats = (clone $baseQuery)
            ->select(
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_mhs'),
                DB::raw('COUNT(DISTINCT akd_penawaran_matakuliah.kode_dosen) as total_dosen'),
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as overall_avg')
            )
            ->first();

        $overallAvg = $summaryStats && $summaryStats->overall_avg !== null ? round((float)$summaryStats->overall_avg, 2) : 0;
        $overallPercent = round(($overallAvg / 4) * 100, 2);
        $totalMhs = $summaryStats ? (int)$summaryStats->total_mhs : 0;
        $totalDosen = $summaryStats ? (int)$summaryStats->total_dosen : 0;

        // Top & Bottom List (rata-rata per dosen, exclude jawaban 0 = Tidak Berlaku)
        $scoreQuery = (clone $baseQuery)
            ->whereNotNull('simpeg_pegawai.id')
            ->whereNotNull('simpeg_pegawai.nama')
            ->select(
                'simpeg_pegawai.id',
                'simpeg_pegawai.nama',
                'simpeg_pegawai.nip',
                DB::raw('AVG(CASE WHEN edom_jawaban.jawaban > 0 THEN edom_jawaban.jawaban ELSE NULL END) as avg_score'),
                DB::raw('COUNT(DISTINCT edom_jawaban.user_id) as total_responden'),
                DB::raw('COUNT(CASE WHEN edom_jawaban.jawaban > 0 THEN 1 END) as valid_responses')
            )
            ->groupBy('simpeg_pegawai.id', 'simpeg_pegawai.nama', 'simpeg_pegawai.nip')
            ->havingRaw('COUNT(CASE WHEN edom_jawaban.jawaban > 0 THEN 1 END) > 0');

        $allScores = $scoreQuery->get();

        // Terapkan kuorum responden jika data mencukupi (misal: min 3 responden), jika belum ada yang memenuhi kuorum, fallback ke semua dosen
        $quorumThreshold = 3;
        $qualifiedScores = $allScores->filter(function($row) use ($quorumThreshold) {
            return (int)$row->total_responden >= $quorumThreshold;
        });

        $activeScoreList = $qualifiedScores->count() >= 3 ? $qualifiedScores : $allScores;

        // Sort secara numerik berdasarkan avg_score DESC
        $sortedDesc = $activeScoreList->sortByDesc(function($row) {
            return (float)$row->avg_score;
        })->values();

        $topList = $sortedDesc->take(3)->map(function($row){
            $avg = round((float)$row->avg_score, 2);
            return [
                'id' => $row->id,
                'nama' => $row->nama,
                'nip' => $row->nip ?: '-',
                'nilai' => $avg,
                'persen' => round(($avg / 4) * 100, 2),
                'total_responden' => (int)$row->total_responden,
                'valid_responses' => (int)$row->valid_responses
            ];
        })->values();

        // Bottom list: Cegah tumpang tindih jika total dosen <= 3
        if ($sortedDesc->count() > 3) {
            $sortedAsc = $sortedDesc->reverse()->take(3)->values();
            $bottomList = $sortedAsc->map(function($row){
                $avg = round((float)$row->avg_score, 2);
                return [
                    'id' => $row->id,
                    'nama' => $row->nama,
                    'nip' => $row->nip ?: '-',
                    'nilai' => $avg,
                    'persen' => round(($avg / 4) * 100, 2),
                    'total_responden' => (int)$row->total_responden,
                    'valid_responses' => (int)$row->valid_responses
                ];
            });
        } else {
            $bottomList = collect([]);
        }

        return response()->json([
            'session_active' => true,
            'pieData' => $pieData,
            'total' => $total,
            'total_valid' => $totalValid,
            'total_na' => $totalNa,
            'total_mhs' => $totalMhs,
            'total_dosen' => $totalDosen,
            'overall_avg' => $overallAvg,
            'overall_percent' => $overallPercent,
            'topList' => $topList,
            'bottomList' => $bottomList,
            'quorum_applied' => $qualifiedScores->count() >= 3
        ]);
    }
    


}
