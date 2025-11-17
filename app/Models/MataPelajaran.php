<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'guru_id',
        'durasi_menit',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guru(){
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwals(){
        return $this->hasMany(Jadwal::class);
    }
}
