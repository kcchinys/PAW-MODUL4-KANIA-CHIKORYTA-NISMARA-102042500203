@extends('layouts.app')

@section('content')
<div style="padding: 24px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; max-width: 1200px; margin: 0 auto;">

    {{-- Tombol Kembali --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route('courses.show', $assignment->course_id) }}" style="text-decoration: none; color: #6b7280; font-weight: 600; font-size: 14px;">
            &larr; Kembali ke Mata Kuliah
        </a>
    </div>

    {{-- Card Header Tugas --}}
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
            <div>
                <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;">{{ $assignment->course->kode_mk }} - {{ $assignment->course->nama_mk }}</div>
                <h3 style="margin: 0 0 8px 0; font-size: 20px; color: #111827; font-weight: 700;">{{ $assignment->judul }}</h3>
                <div style="font-size: 13px; color: #6b7280;">
                    Batas Pengumpulan: {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('l, d F Y - H:i') }} WIB 
                    <span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px;">Sedang Berlangsung</span>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                {{-- TODO 7b: Tambahkan tombol Export CSV yang mengarah ke route('assignments.export-csv', $assignment) --}}
                <a href="{{ route('assignments.export-csv', $assignment) }}" style="background: #dc2626; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600;">Export CSV</a>
                <a href="#" style="background: #fff; border: 1px solid #d1d5db; color: #374151; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600;">Edit Tugas</a>
            </div>
        </div>
        <div style="border-top: 1px solid #e5e7eb; margin: 20px 0; padding-top: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Petunjuk Pengerjaan:</div>
            <div style="font-size: 14px; color: #4b5563; line-height: 1.5;">{{ $assignment->deskripsi }}</div>
        </div>
    </div>

    {{-- Statistik Cards --}}
    {{-- TODO 7a: Tampilkan kartu ringkasan Total Mahasiswa, Jumlah Mengumpulkan, dan Rata-rata Nilai --}}
    <div style="display: flex; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 8px;">Total Mahasiswa</div>
            <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ $assignment->course->approvedStudents->count() }}</div>
        </div>
        <div style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 8px;">Sudah Mengumpulkan</div>
            <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ $submissions->count() }}</div>
        </div>
        <div style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 8px;">Rata-rata Nilai</div>
            <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ number_format($submissions->avg('nilai') ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- Tabel Rekap --}}
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e5e7eb;">
            <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #111827;">Rekapitulasi Pengumpulan & Penilaian Mahasiswa</h4>
            <div style="font-size: 13px; color: #6b7280;">Semua mahasiswa yang terdaftar resmi di kelas mata kuliah ini</div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; text-align: center; width: 5%;">NO</th>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; width: 22%;">MAHASISWA</th>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; width: 12%;">NIM</th>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; width: 25%;">STATUS & WAKTU</th>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; width: 15%;">JAWABAN TEKS</th>
                        <th style="padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 700; width: 21%;">PENILAIAN DOSEN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $index => $sub)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 16px; text-align: center; color: #6b7280; font-weight: 600;">{{ $index + 1 }}</td>
                            <td style="padding: 16px;">
                                <div style="font-weight: 700; color: #111827; margin-bottom: 2px;">{{ $sub->user->name }}</div>
                                <div style="font-size: 12px; color: #6b7280;">{{ $sub->user->email }}</div>
                            </td>
                            <td style="padding: 16px; color: #4b5563;">{{ $sub->user->nomor_induk }}</td>
                            <td style="padding: 16px;">
                                <div style="font-size: 12px; font-weight: 600; color: #059669; margin-bottom: 4px;">Sudah Mengumpulkan</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 6px;">{{ \Carbon\Carbon::parse($sub->submitted_at)->translatedFormat('d M Y, H:i') }} WIB</div>
                                @if($sub->nilai !== null)
                                    <span style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ $sub->nilai }}/100 ({{ $sub->status_nilai }})</span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                <a href="#" style="color: #dc2626; font-weight: 600; text-decoration: none; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#jawabanModal{{ $sub->id }}">
                                    Lihat Jawaban ({{ strlen($sub->jawaban) }} Karakter)
                                </a>
                            </td>
                            <td style="padding: 16px;">
                                {{-- TODO 7c: Tambahkan form ber-method PATCH menuju route('submissions.grade', $sub->id) berisi input nilai (0–100), input catatan_dosen, dan tombol simpan --}}
                                <form action="{{ route('submissions.grade', $sub->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center; margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="nilai" value="{{ $sub->nilai }}" min="0" max="100" style="width: 60px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; text-align: center;" placeholder="0">
                                    <input type="text" name="catatan_dosen" value="{{ $sub->catatan_dosen }}" style="width: 110px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;" placeholder="Catatan...">
                                    <button type="submit" style="background: #dc2626; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">Simpan Nilai</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 32px; text-align: center; color: #6b7280;">Belum ada mahasiswa yang mengumpulkan tugas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@foreach ($submissions as $sub)
    <div class="modal fade" id="jawabanModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Jawaban: {{ $sub->user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div style="padding: 16px; background: #f3f4f6; border-radius: 6px; color: #111827; white-space: pre-wrap; line-height: 1.6;">{{ $sub->jawaban }}</div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection