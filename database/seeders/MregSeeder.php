<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MregSeeder extends Seeder
{
    public function run()
    {
        DB::table('akd_mreg')->insert([
            ['tahun' => '2022', 'semester' => '1', 'tahun_akademik' => '2022/2023', 'trash' => 0],
            ['tahun' => '2022', 'semester' => '2', 'tahun_akademik' => '2022/2023', 'trash' => 0],
            ['tahun' => '2023', 'semester' => '1', 'tahun_akademik' => '2023/2024', 'trash' => 0],
            ['tahun' => '2023', 'semester' => '2', 'tahun_akademik' => '2023/2024', 'trash' => 1],
            ['tahun' => '2024', 'semester' => '1', 'tahun_akademik' => '2024/2025', 'trash' => 0],
            ['tahun' => '2024', 'semester' => '2', 'tahun_akademik' => '2024/2025', 'trash' => 0]
        ]);
    }
}

