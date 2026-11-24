<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu 
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dewi Marini Umi Atmaja, S.Kom., M.Kom.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'kania@celoe.test'],
            [
                'name' => 'Kania Chikoryta Nismara',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '102042500203',
            ]
        );

        // 3. Mata Kuliah Dasar 
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission

        // Tambah 1 mahasiswa baru
        $mahasiswaBaru = User::create([
            'name' => 'Suna Rintarou',
            'email' => 'suna@celoe.test',
            'password' => bcrypt('mahasiswa123'),
            'role' => 'mahasiswa',
            'nomor_induk' => '1301210002',
        ]);

        // Tambah 2 matkul dengan semester & SKS berbeda
        $mk1 = MataKuliah::create([
            'kode_mk' => 'IF401',
            'nama_mk' => 'Pemrograman Web Lanjut',
            'deskripsi' => 'Mata kuliah lanjutan tentang pengembangan web modern.',
            'sks' => 3,
            'semester' => 'Ganjil 2026/2027',
            'dosen_id' => $dosen->id, 
        ]);

        $mk2 = MataKuliah::create([
            'kode_mk' => 'IF402',
            'nama_mk' => 'Basis Data',
            'deskripsi' => 'Mata kuliah tentang optimasi dan normalisasi database.',
            'sks' => 2,
            'semester' => 'Genap 2026/2027',
            'dosen_id' => $dosen->id,
        ]);

        // Daftarkan mahasiswa baru ke kedua mata kuliah (status approved)
        $mk1->students()->attach($mahasiswaBaru->id, ['status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        $mk2->students()->attach($mahasiswaBaru->id, ['status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);

        // Tambah 2 tugas (satu per mata kuliah)
        $tugas1 = Assignment::create([
            'course_id' => $mk1->id,
            'judul' => 'Tugas 1: Implementasi Middleware & Eloquent ORM',
            'deskripsi' => 'Jelaskan cara kerja Route Middleware untuk proteksi peran serta implementasi relasi Eloquent.',
            'deadline' => now()->addDays(7),
        ]);

        $tugas2 = Assignment::create([
            'course_id' => $mk2->id,
            'judul' => 'Tugas 1: Normalisasi Database',
            'deskripsi' => 'Lakukan normalisasi 1NF, 2NF, dan 3NF pada studi kasus yang diberikan.',
            'deadline' => now()->addDays(7),
        ]);

        // Contoh data submission untuk mahasiswa baru
        Submission::create([
            'assignment_id' => $tugas1->id,
            'user_id' => $mahasiswaBaru->id,
            'jawaban' => 'Middleware berfungsi sebagai lapisan pengecekan sebelum request masuk ke controller...',
            'submitted_at' => now(),
        ]);

        Submission::create([
            'assignment_id' => $tugas2->id,
            'user_id' => $mahasiswaBaru->id,
            'jawaban' => 'Normalisasi database dilakukan untuk menghilangkan redundansi data...',
            'submitted_at' => now(),
        ]);
            }
}
