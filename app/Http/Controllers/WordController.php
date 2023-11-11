<?php

namespace App\Http\Controllers;

use App\Models\word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        try {
            $userId = Auth::id(); // Mendapatkan ID user yang terautentikasi

            $userWords = Word::where('user_id', $userId)->get(); // Mengambil kata-kata berdasarkan ID user

            return response([
                "status_code" => 200,
                "message" => "Berhasil",
                "data" => $userWords
            ]);
        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Terjadi kesalahan pada server",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user(); // Mendapatkan user yang sedang terautentikasi
            $data = $request->all();
            $data['user_id'] = $user->id; // Mengatur user_id sesuai dengan ID user yang terautentikasi

            $word = Word::create($data);

            return response([
                "status_code" => 201,
                "message" => "Data berhasil dibuat",
                "data" => $word
            ]);
        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Terjadi kesalahan pada server",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
{
    try {
        $user = Auth::user(); // Mendapatkan user yang terautentikasi
        $word = Word::where('user_id', $user->id)->find($id); // Mencari kata yang sesuai dengan ID user dan ID kata

        if (!$word) {
            return response([
                "status_code" => 404,
                "message" => "Data tidak ditemukan",
            ]);
        }

        $word->update($request->all());

        return response([
            "status_code" => 200,
            "message" => "Data berhasil diperbarui",
            "data" => $word
        ]);
    } catch (\Exception $e) {
        return response([
            "status_code" => 500,
            "message" => "Terjadi kesalahan pada server",
            "error" => $e->getMessage()
        ]);
    }
}

public function destroy($id)
{
    try {
        $user = Auth::user(); // Mendapatkan user yang terautentikasi
        $word = Word::where('user_id', $user->id)->find($id); // Mencari kata yang sesuai dengan ID user dan ID kata

        if (!$word) {
            return response([
                "status_code" => 404,
                "message" => "Data tidak ditemukan",
            ]);
        }

        $word->delete();

        return response([
            "status_code" => 200,
            "message" => "Data berhasil dihapus",
        ]);
    } catch (\Exception $e) {
        return response([
            "status_code" => 500,
            "message" => "Terjadi kesalahan pada server",
            "error" => $e->getMessage()
        ]);
    }
}
}
