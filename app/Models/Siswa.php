<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = [
        'kelas_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'phone',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kelas(){
        return $this->belongsTo(Kelas::class);
    }

    public function absensis(){
        return $this->hasMany(Absensi::class);
    }
}
