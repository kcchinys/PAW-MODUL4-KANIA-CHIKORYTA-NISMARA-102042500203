<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isMahasiswa()) {
            abort(403, 'Akses ditolak. Pengumpulan tugas hanya dapat dilakukan oleh Mahasiswa.');
        }

        $user = Auth::user();

        $enrollment = $assignment->course->students()->where('users.id', $user->id)->first();
        if (!$enrollment || $enrollment->pivot->status !== 'approved') {
            abort(403, 'Akses ditolak. Anda belum disetujui oleh Dosen untuk mengikuti mata kuliah tugas ini.');
        }

        if ($assignment->isOverdue()) {
            return back()->with('error', 'Gagal mengumpulkan: Batas waktu pengumpulan (deadline) tugas telah berakhir.');
        }

        $validated = $request->validate([
            'jawaban' => ['required', 'string', 'min:10', 'max:20000'],
        ], [
            'jawaban.required' => 'Teks jawaban tugas wajib diisi.',
            'jawaban.min' => 'Jawaban terlalu pendek. Tuliskan jawaban minimal 10 karakter.',
            'jawaban.max' => 'Jawaban melebihi batas maksimal 20.000 karakter.',
        ]);

        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
            ],
            [
                'jawaban' => $validated['jawaban'],
                'submitted_at' => now(),
            ]
        );

        return back()->with('success', 'Jawaban tugas Anda berhasil disimpan ke sistem.');
    }

       public function exportCsv(Assignment $assignment): StreamedResponse
    {
        // TODO 5b (exportCsv): Cek kepemilikan dosen, lalu gunakan response()->streamDownload() dan fputcsv() untuk mengunduh data nilai mahasiswa.
        // 1. Pastikan $assignment->course->dosen_id === Auth::id(), jika tidak abort(403).
        if ($assignment->course->dosen_id !== Auth::id()) {
            abort(403);
        }

        // 2. Muat relasi course.approvedStudents dan submissions dari $assignment.
        // 3. Kembalikan response()->streamDownload(...) yang menulis header & baris CSV menggunakan fopen('php://output', 'w') dan fputcsv().
        $submissions = $assignment->submissions()->with('user')->get();
        $filename = 'rekap_nilai_' . $assignment->id . '.csv';

        return response()->streamDownload(function () use ($submissions) {
            $handle = fopen('php://output', 'w');
            
            // Tambahkan BOM UTF-8 biar karakter aman di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); 
            
            // PAKAI TITIK KOMA (;) SEBAGAI PEMISAH, BUKAN KOMA (,)
            fputcsv($handle, ['NIM', 'Nama Mahasiswa', 'Nilai', 'Catatan Dosen'], ';');
            
            foreach ($submissions as $sub) {
                // Cek pakai nomor_induk, kalau kosong coba panggil nim, kalau dua-duanya kosong kasih strip '-'
                $nim = $sub->user->nomor_induk ?? $sub->user->nim ?? '-';

                fputcsv($handle, [
                    $nim,
                    $sub->user->name, 
                    $sub->nilai ?? 'Belum Dinilai', 
                    $sub->catatan_dosen ?? '-'
                ], ';');
            }
            fclose($handle);
        }, $filename);
    }
}