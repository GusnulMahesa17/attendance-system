<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'pertemuan_id',
        'siswa_id',
        'status',
        'keterangan',
    ];

    public function pertemuan(){
        return $this->belongsTo(Pertemuan::class);
    }

    public function siswa(){
        return $this->belongsTo(Siswa::class);
    }
}
