<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        // Tabel users (admin & guru)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'guru'])->default('guru');
            $table->string('nip')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('tingkat');
            $table->string('jurusan')->nullable();
            $table->year('tahun_ajaran');
            $table->timestamps();
        });

        // Tabel siswa
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('nis')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // Tabel mata pelajaran
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('durasi_menit')->default(90);
            $table->string('hari')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->timestamps();
        });

        // Tabel jadwal (relasi kelas dan mapel)
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });

        // Tabel pertemuan
        Schema::create('pertemuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('pertemuan_ke');
            $table->text('deskripsi')->nullable();
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->year('tahun_ajaran');
            $table->timestamps();
        });

        // Tabel absensi
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuans')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensis');
        Schema::dropIfExists('pertemuans');
        Schema::dropIfExists('jadwals');
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('users');
    }
};
