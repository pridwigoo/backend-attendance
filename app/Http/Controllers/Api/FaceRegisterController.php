<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FaceRecognition\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceRegisterController extends Controller
{
    protected $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function registerFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        $user = $request->user();

        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('image')->store('faces', 'public');
        $this->faceService->registerFace($user, $path);

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi wajah berhasil disimpan.',
            'data'    => [
                'profile_photo_url' => asset('storage/' . $path),
            ],
        ], 200);
    }
}