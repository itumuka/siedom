<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mreg extends Model
{
    use HasFactory;

    protected $table = 'akd_mreg';
    protected $primaryKey = 'id_mreg';
    public $timestamps = false;

    protected $fillable = [
        'tahun', 'semester', 'tahun_akademik', 'trash'
    ];
}
