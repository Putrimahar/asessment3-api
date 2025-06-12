<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MakananController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->header('Authorization');

        if ($userId) {
            $data = Makanan::where('email', $userId)
                ->orWhereNull('email')
                ->get()
                ->map(function ($item) use ($userId) {
                    $item->mine = $item->email === $userId ? 1 : 0;
                    return $item;
                });
        } else {
            $data = Makanan::whereNull('email')
                ->get()
                ->map(function ($item) {
                    $item->mine = 0;
                    return $item;
                });
        }

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $email = $request->header('Authorization'); // <- ambil dari header
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('gambar')->store('gambar-makanan', 'public');

        Makanan::create([
            'nama' => $request->nama,
            'gambar' => $path,
            'email' => $email,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $email = $request->header('Authorization'); // Ambil email dari header

        // Validasi data input
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Cari data berdasarkan id dan email
        $makanan = Makanan::where('id', $id)
            ->where('email', $email)
            ->first();

        if (!$makanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Update nama
        $makanan->nama = $request->nama;

        // Jika ada gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($makanan->gambar && Storage::disk('public')->exists($makanan->gambar)) {
                Storage::disk('public')->delete($makanan->gambar);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('gambar-makanan', 'public');
            $makanan->gambar = $path;
        }

        // Simpan perubahan
        $makanan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $email = $request->header('Authorization'); // Ambil email dari header

        // Cari data berdasarkan id dan email
        $makanan = Makanan::where('id', $id)
            ->where('email', $email)
            ->first();

        if (!$makanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Hapus file gambar jika ada
        if ($makanan->gambar && Storage::disk('public')->exists($makanan->gambar)) {
            Storage::disk('public')->delete($makanan->gambar);
        }

        // Hapus data dari database
        $makanan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}
