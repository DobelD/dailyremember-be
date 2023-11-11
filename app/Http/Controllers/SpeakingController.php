<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\speaking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SpeakingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        try {
            $userId = Auth::id(); // Mendapatkan ID user yang terautentikasi
            $userSpeaking = speaking::where('user_id', $userId)->get(); // Mengambil kata-kata berdasarkan ID user

            return response([
                "status_code" => 200,
                "message" => "Berhasil",
                "data" => $userSpeaking
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
            $user = Auth::user();
            $data = $request->all();
            $data['user_id'] = $user->id;
            $pathToFile = $request->file('file')->path();
            $transcriptionId = $this->createdTranscribe($pathToFile, $request->title);
            if ($transcriptionId) {
                $data['id_transcript'] = $transcriptionId;
                $speaking = Speaking::create($data);
                return response([
                    "status_code" => 201,
                    "message" => "Data berhasil dibuat",
                    "data" => $speaking,
                ]);
            } else {
                return response([
                    "status_code" => 500,
                    "message" => "Gagal melakukan transkripsi audio",
                ]);
            }
        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Terjadi kesalahan pada server",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function createdTranscribe($path, $title)
    {
        try {
            $apiKey = "qAsrogrA0RY21ItLJPnrUouUmEG6MWUC"; // Ganti dengan kunci API Speechmatics Anda
            $url = 'https://asr.api.speechmatics.com/v2/jobs/';

            $client = new Client();

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => [
                    [
                        'name' => 'data_file',
                        'contents' => fopen($path, 'r'),
                        'filename' => $title,
                    ],
                    [
                        'name' => 'config',
                        'contents' => json_encode([
                            'type' => 'transcription',
                            'transcription_config' => [
                                'operating_point' => 'enhanced',
                                'language' => 'en',
                            ],
                        ]),
                    ],
                ],
            ]);

            if ($response->getStatusCode() == 201) {
                $responseData = json_decode($response->getBody(), true);
                return $responseData['id'];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Server error",
                "error" => $e->getMessage()
            ]);
        }
    }


    public function update(Request $request, $id)
    {
    try {
        $user = Auth::user(); // Mendapatkan user yang terautentikasi
        $speaking = speaking::where('user_id', $user->id)->find($id); // Mencari kata yang sesuai dengan ID user dan ID kata

        if (!$speaking) {
            return response([
                "status_code" => 404,
                "message" => "Data tidak ditemukan",
            ]);
        }

        $speaking->update($request->all());

        return response([
            "status_code" => 200,
            "message" => "Data berhasil diperbarui",
            "data" => $speaking
        ]);
    } catch (\Exception $e) {
        return response([
            "status_code" => 500,
            "message" => "Terjadi kesalahan pada server",
            "error" => $e->getMessage()
        ]);
    }
    }

    public function destroy($id, $idTranscribe)
    {
        try {
            $user = Auth::user();
            $speaking = Speaking::where('user_id', $user->id)->find($id);

            $url = 'https://asr.api.speechmatics.com/v2/jobs/' . $idTranscribe;

            if (!$speaking) {
                return response([
                    "status_code" => 404,
                    "message" => "Data tidak ditemukan",
                ]);
            } else {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer qAsrogrA0RY21ItLJPnrUouUmEG6MWUC',
                ])->delete($url);

                if ($response->successful()) {
                    $speaking->delete();
                    return response([
                        "status_code" => 200,
                        "message" => "Data berhasil dihapus",
                    ]);
                } else {
                    return response([
                        "status_code" => $response->status(),
                        "message" => "Gagal menghapus data dari Speechmatics",
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Terjadi kesalahan pada server",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function addTranscribeToSpeaking($id,$idTranscribe)
    {
        try {
            $user = Auth::user();
            $speaking = Speaking::where('user_id', $user->id)->find($id);

            $url = 'https://asr.api.speechmatics.com/v2/jobs/' . $idTranscribe . '/transcript?format=txt';

            if (!$speaking) {
                return response([
                    "status_code" => 404,
                    "message" => "Data tidak ditemukan",
                ]);
            } else {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer qAsrogrA0RY21ItLJPnrUouUmEG6MWUC',
                ])->get($url);

                if ($response->successful()) {
                    $speaking->transcript = $response;
                    $speaking->update();
                    return response([
                        "status_code" => 200,
                        "message" => "Data berhasil diupdate",
                        "data" => $speaking
                    ]);
                } else {
                    return response([
                        "status_code" => $response->status(),
                        "message" => "Gagal menghapus data",
                    ]);
                }
            }

        } catch (\Exception $e) {
            return response([
                "status_code" => 500,
                "message" => "Terjadi kesalahan pada server",
                "error" => $e->getMessage()
            ]);
        }
    }
}
