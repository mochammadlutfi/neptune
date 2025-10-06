<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Master\Vessel;
use App\Http\Resources\Master\VesselResource;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): JsonResponse
    {
        $id = auth()->user()->id;

        $data = User::with('vessel', 'vessels')->where('id', $id)->first();

        $data->permissions = $data->getPermissionsViaRoles()->pluck('name');
        
        return response()->json($data,200);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): JsonResponse
    {
        // Implementation for profile update
        // TODO: Implement profile update logic
        return response()->json(['message' => 'Profile update not implemented yet'], 501);
    }

    /**
     * Update vessel aktif untuk user
     * @param Request $request
     * @return JsonResponse
     */
    public function updateVessel(Request $request): JsonResponse
    {
        $request->validate([
            'vessel_id' => 'nullable|exists:vessels,id'
        ]);

        $user = auth()->user();
        $user->vessel_id = $request->vessel_id;
        $user->save();

        // Load vessel data untuk response
        $user->load('vessel');

        return response()->json([
            'success' => true,
            'message' => 'Vessel berhasil diupdate',
            'data' => [
                'vessel_id' => $user->vessel_id,
                'vessel' => $user->vessel ? new VesselResource($user->vessel) : null
            ]
        ]);
    }

    /**
     * Mendapatkan daftar vessel yang dapat diakses user
     * @param Request $request
     * @return JsonResponse
     */
    public function getVessels(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        // Untuk saat ini, semua user bisa akses semua vessel aktif
        // Nanti bisa dikustomisasi berdasarkan role atau assignment
        $vessels = Vessel::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => VesselResource::collection($vessels),
            'current_vessel_id' => $user->vessel_id
        ]);
    }


    public function device(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = $user->tokens()
            ->select('id', 'name', 'ip', 'last_used_at')
            ->orderBy('last_used_at', 'DESC')
            ->get();

        $currentToken = $user->currentAccessToken();

        foreach ($devices as $device) {
            $device->hash = Crypt::encryptString($device->id);

            if ($currentToken->id === $device->id) {
                $device->is_current = true;
            }

            unset($device->id);
        }
        
        return response()->json($devices);
    }

}
