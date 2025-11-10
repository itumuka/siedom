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
    Schema::create('akd_mreg', function (Blueprint $table) {
        $table->id('id_mreg');
        $table->string('tahun', 10)->nullable();
        $table->string('semester', 1);
        $table->string('tahun_akademik', 15)->nullable();
        $table->integer('trash')->default(0);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mregs');
    }
};
