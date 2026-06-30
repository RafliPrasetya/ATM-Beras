<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();

        return view(
            'admin.berita.index',
            compact('news')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'status' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {
            $gambar = $request
                ->file('gambar')
                ->store('berita', 'public');
        }

        News::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'gambar' => $gambar,
            'status' => $request->status,
            'created_by' => session('admin_id'),
        ]);

        return back()->with(
            'success',
            'Berita berhasil ditambahkan'
        );
    }

    public function update(Request $request, News $beritum)
    {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'status' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'judul' => $request->judul,
            'konten' => $request->konten,
            'status' => $request->status,
        ];

        if ($request->hasFile('gambar')) {

            // Hapus gambar lama kalau ada
            if ($beritum->gambar && Storage::disk('public')->exists($beritum->gambar)) {
                Storage::disk('public')->delete($beritum->gambar);
            }

            // Upload gambar baru
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $beritum->update($data);

        return back()->with(
            'success',
            'Berita berhasil diupdate'
        );
    }

    public function destroy(News $beritum)
    {
        // Hapus gambar dari storage kalau ada
        if ($beritum->gambar && Storage::disk('public')->exists($beritum->gambar)) {
            Storage::disk('public')->delete($beritum->gambar);
        }

        // Hapus data berita dari database
        $beritum->delete();

        return back()->with(
            'success',
            'Berita berhasil dihapus'
        );
    }
}
