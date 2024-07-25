<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Mahasiswa extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $table = 'akd_mahasiswa';
    protected $primaryKey = 'id_mhs';
    public $timestamps = true;

    protected $fillable = [
        'nim', 'nik_mhs', 'no_pendaftaran', 'tgl_registrasi', 'tahun_angkatan',
        'semester', 'kode_jalur_pmb', 'kode_program_pendidikan', 'kode_program_studi',
        'kode_fakultas', 'tempat_lahir', 'tanggal_lahir', 'nama_mahasiswa', 'alamat_asal',
        'rt', 'rw', 'kode_pos', 'kode_provinsi', 'kode_kabupaten', 'kode_agama', 'kode_kewarganegaraan',
        'status_nikah', 'jenis_kelamin', 'tahun_kurikulum', 'foto', 'status_mhs',
        'password_ortu', 'password_mhs', 'model_pembayaran', 'dispensasi', 'status_wali', 'trash',
        'lulus', 'import_alumni', 'beasiswa', 'id_dosen_wali', 'jenis_pembayaran', 'jenis_pembayaran_keu',
        'telp', 'email', 'pendidikan_terakhir', 'alamat_slta', 'jurusan_slta', 'no_ijazah_slta', 'tahun_ijazah_slta'
    ];

    protected $hidden = [
        'password_mhs',
    ];
}

