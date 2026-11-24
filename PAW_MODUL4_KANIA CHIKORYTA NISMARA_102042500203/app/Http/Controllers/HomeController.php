<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        // TODO 3: Lengkapi logika index():
        // 1. Buat query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        $query = Buku::query();
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // 2. Tambahkan filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->penerbit);
        }

        if ($request->filled('filter_stok')) {
            if ($request->filter_stok == 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->filter_stok == 'habis') {
                $query->where('stok', '=', 0);
            }
        }

        // 3. Tambahkan pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        $sort = $request->get('sort', 'latest');

        switch ($sort) {
            case 'judul_asc':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;
            case 'tahun_asc':
                $query->orderBy('tahun_terbit', 'asc');
                break;
            case 'tahun_desc':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'stok_asc':
                $query->orderBy('stok', 'asc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            case 'latest':
            default:
                $query->latest(); 
                break;
}
        
        // 4. Lakukan paginasi 8 data dengan withQueryString()
         $buku = $query->paginate(8)->withQueryString();

        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        $listPenerbit = Buku::query()
            ->select('penerbit')
            ->distinct()
            ->orderBy('penerbit', 'asc')
            ->pluck('penerbit');

        // 6. Return view 'home' dengan data buku dan list penerbit

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
