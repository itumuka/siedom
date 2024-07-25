<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('akd_mahasiswa', function (Blueprint $table) {
            $table->increments('id_mhs');
            $table->string('nim', 20)->unique()->nullable();
            $table->string('nik_mhs', 25)->nullable();
            $table->string('no_pendaftaran', 20)->unique()->nullable();
            $table->date('tgl_registrasi');
            $table->year('tahun_angkatan');
            $table->integer('semester')->comment('1 = ganjil, 2 = genap');
            $table->string('kode_jalur_pmb', 2)->nullable();
            $table->string('kode_program_pendidikan', 2)->nullable();
            $table->string('kode_program_studi', 5)->nullable();
            $table->integer('kode_fakultas')->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nama_mahasiswa', 100)->nullable();
            $table->string('alamat_asal', 200)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('kode_provinsi', 5)->nullable();
            $table->string('kode_kabupaten', 5)->nullable();
            $table->string('kode_agama', 2)->nullable();
            $table->string('kode_kewarganegaraan', 2)->nullable();
            $table->string('status_nikah', 2)->nullable();
            $table->string('jenis_kelamin', 5)->nullable();
            $table->string('tahun_kurikulum', 20)->nullable();
            $table->text('foto')->nullable();
            $table->string('status_mhs', 2)->nullable();
            $table->string('password_ortu', 32)->nullable();
            $table->string('password_mhs')->nullable();
            $table->string('model_pembayaran', 2)->nullable();
            $table->integer('dispensasi')->nullable()->default(0);
            $table->integer('status_wali')->default(0);
            $table->integer('trash')->nullable()->default(0);
            $table->integer('lulus')->nullable()->default(0)->comment('0:aktif;1:lulus;2:mengundurkandiri;3:dikeluarkan;');
            $table->integer('import_alumni')->nullable()->default(0);
            $table->integer('beasiswa')->nullable()->default(0);
            $table->integer('id_dosen_wali')->nullable();
            $table->string('jenis_pembayaran', 100)->nullable();
            $table->string('jenis_pembayaran_keu', 100)->nullable();
            $table->string('telp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pendidikan_terakhir', 200)->nullable();
            $table->text('alamat_slta')->nullable();
            $table->string('jurusan_slta', 45)->nullable();
            $table->string('no_ijazah_slta', 25)->nullable();
            $table->string('tahun_ijazah_slta', 10)->nullable();
            $table->timestamps();

            $table->index('tahun_angkatan');
            $table->index('kode_program_studi');
            $table->index('kode_fakultas');
            $table->index('tahun_kurikulum');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahasiswas');
    }
};
