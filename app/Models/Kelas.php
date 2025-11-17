<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'tahun_ajaran',
    ];

    public function siswas(){
        return $this->hasMany(Siswa::class);
    }

    public function jadwals(){
        return $this->hasMany(Jadwal::class);
    }
}
