
    public function prodiReportView()
    {
        return view('admin.report.prodi');
    }

    // Universal aggregate untuk prodi (pie)
    public function reportProdiUniversal(Request $request)
    {
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        $labels = [0=>'Tidak Berlaku',1=>'Sangat Tidak Sesuai',2=>'Tidak Sesuai',3=>'Sesuai',4=>'Sangat Sesuai'];

        $query = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select('edom_jawaban.jawaban', DB::raw('COUNT(*) as count'))
            ->groupBy('edom_jawaban.jawaban')
            ->get();

        $total = $query->sum('count');
        $pieData = [];
        foreach ($labels as $k=>$v) {
            $r = $query->firstWhere('jawaban', $k);
            $cnt = $r ? $r->count : 0;
            $pieData[] = ['name'=>$v,'value'=>$cnt,'percentage'=> $total ? round(($cnt/$total)*100,2) : 0];
        }

        return response()->json(['pieData'=>$pieData,'total'=>$total]);
    }

    // Per-dosen summary (avg per dosen di prodi)
    public function reportProdiPerDosen(Request $request)
    {
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->join('simpeg_pegawai','akd_penawaran_matakuliah.kode_dosen','=','simpeg_pegawai.id')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select('simpeg_pegawai.id as id_pegawai','simpeg_pegawai.nama as nama','simpeg_pegawai.nip', DB::raw('AVG(edom_jawaban.jawaban) as avg_score'), DB::raw('COUNT(*) as total_responses'))
            ->groupBy('simpeg_pegawai.id','simpeg_pegawai.nama','simpeg_pegawai.nip')
            ->orderBy('avg_score','desc')
            ->get();

        $list = $rows->map(function($r){
            return [
                'id_pegawai'=>$r->id_pegawai,
                'nama'=>$r->nama,
                'nip'=>$r->nip,
                'avg'=> round($r->avg_score,2),
                'percent' => round(($r->avg_score/4)*100,2),
                'responses' => (int)$r->total_responses
            ];
        });

        return response()->json(['list'=>$list]);
    }

    // Per-kelas untuk dosen (drilldown) — parameter id_dosen
    public function reportProdiPerKelas(Request $request)
    {
        $id_dosen = $request->input('id_dosen');
        $kode_prodi = Session::get('kode_program_studi');
        $id_mreg = Session::get('id_mreg');

        if (!$id_dosen) {
            return response()->json(['error'=>'id_dosen required'],400);
        }

        $rows = DB::table('edom_jawaban')
            ->join('akd_kelas_kuliah','edom_jawaban.id_kelas','=','akd_kelas_kuliah.id_kelas')
            ->join('akd_penawaran_matakuliah','akd_kelas_kuliah.id_tawar','=','akd_penawaran_matakuliah.id_tawar')
            ->join('akd_matakuliah','akd_penawaran_matakuliah.id_matakuliah','=','akd_matakuliah.id_matakuliah')
            ->where('akd_penawaran_matakuliah.kode_program_studi', $kode_prodi)
            ->where('akd_penawaran_matakuliah.kode_dosen', $id_dosen)
            ->where('edom_jawaban.id_mreg', $id_mreg)
            ->select('akd_kelas_kuliah.id_kelas','akd_kelas_kuliah.nama_kelas','akd_matakuliah.nama_matakuliah', DB::raw('AVG(edom_jawaban.jawaban) as avg_score'), DB::raw('COUNT(*) as total_responses'))
            ->groupBy('akd_kelas_kuliah.id_kelas','akd_kelas_kuliah.nama_kelas','akd_matakuliah.nama_matakuliah')
            ->orderBy('avg_score','desc')
            ->get();

        $kelas = $rows->map(function($r){
            return [
                'id_kelas'=>$r->id_kelas,
                'nama_kelas'=>$r->nama_kelas,
                'nama_matakuliah'=>$r->nama_matakuliah,
                'avg'=> round($r->avg_score,2),
                'percent'=> round(($r->avg_score/4)*100,2),
                'responses'=> (int)$r->total_responses
            ];
        });

        return response()->json(['kelas'=>$kelas]);
    }
