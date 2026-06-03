<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function rapikan(Request $request)
    {
        // Pastikan return JSON saat error apapun
        try {
            $request->validate([
                'konten' => 'required|string',
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.3-70b-versatile',
                        'max_tokens' => 2000,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Kamu adalah asisten yang membantu merapikan teks spesifikasi teknis dalam Bahasa Indonesia.',
                            ],
                            [
                                'role' => 'user',
                                'content' => "Rapikan teks berikut untuk rich text editor. Gunakan tag HTML sederhana seperti <b>, <i>, <u>, <ol>, <ul>, <li>, <p>, <br>, dsb boleh, tapi JANGAN gunakan tabel (<table>, <tr>, <td>, dll). Jika terdapat elemen tabel dalam teks, ubah menjadi daftar poin-poin atau narasi, dan rapikan agar mudah dibaca dalam format HTML sederhana tanpa format tabel apapun.\n\nTeks:\n" . $request->konten,
                            ],
                        ],
                    ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Groq API gagal.',
                    'status' => $response->status(),
                    'body' => $response->body(),
                ], 500);
            }

            return response()->json([
                'hasil' => $response->json('choices.0.message.content')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
