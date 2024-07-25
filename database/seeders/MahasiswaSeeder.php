<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        DB::table('akd_mahasiswa')->insert([
            'nim' => '1234567890',
            'nik_mhs' => '9876543210',
            'no_pendaftaran' => '20210001',
            'tgl_registrasi' => '2021-08-01',
            'tahun_angkatan' => 2021,
            'semester' => 1,
            'kode_jalur_pmb' => '01',
            'kode_program_pendidikan' => '01',
            'kode_program_studi' => 'CS101',
            'kode_fakultas' => 1,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'nama_mahasiswa' => 'John Doe',
            'alamat_asal' => 'Jl. Merdeka No. 1',
            'rt' => '001',
            'rw' => '002',
            'kode_pos' => '12345',
            'kode_provinsi' => '01',
            'kode_kabupaten' => '02',
            'kode_agama' => '01',
            'kode_kewarganegaraan' => 'ID',
            'status_nikah' => 'M',
            'jenis_kelamin' => 'L',
            'tahun_kurikulum' => '2018',
            'foto' => null,
            'status_mhs' => 'A',
            'password_mhs' => Hash::make('password123'),
            'model_pembayaran' => '01',
            'dispensasi' => 0,
            'status_wali' => 0,
            'trash' => 0,
            'lulus' => 0,
            'import_alumni' => 0,
            'beasiswa' => 0,
            'id_dosen_wali' => null,
            'jenis_pembayaran' => 'Reguler',
            'jenis_pembayaran_keu' => 'Reguler',
            'telp' => '08123456789',
            'email' => 'john.doe@example.com',
            'pendidikan_terakhir' => 'SMA',
            'alamat_slta' => 'Jl. Merdeka No. 1',
            'jurusan_slta' => 'IPA',
            'no_ijazah_slta' => '123456',
            'tahun_ijazah_slta' => '2018',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

