<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    protected $fillable =[
        'jadwal_id',
        'tanggal',
        'pertemuan_ke',
        'deskripsi',
        'semester',
        'tahun_ajaran',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwal(){
        return $this->belongsTo(Jadwal::class);
    }

    public function absensis(){
        return $this->hasMany(Absensi::class);
    }

}
