<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sequence;
use App\Http\Requests\Settings\SequenceFormRequest;

class SequenceController extends Controller
{
    /**
     * Menampilkan daftar semua sequences
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $sequences = Sequence::orderBy('name')->get();
            
            return response()->json([
                'success' => true,
                'result' => $sequences
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sequences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail sequence berdasarkan ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $sequence = Sequence::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'result' => $sequence
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sequence tidak ditemukan',
                'error' => 'Sequence dengan ID ' . $id . ' tidak ada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail sequence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update format sequence (hanya field yang diizinkan)
     * User hanya bisa mengubah format, tidak bisa menambah/hapus sequence
     * 
     * @param \App\Http\Requests\Settings\SequenceFormRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SequenceFormRequest $request, $id)
    {
        try {
            $sequence = Sequence::findOrFail($id);
            
            // Update hanya field yang diizinkan
            $sequence->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Format sequence berhasil diperbarui',
                'result' => $sequence->fresh()
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sequence tidak ditemukan',
                'error' => 'Sequence dengan ID ' . $id . ' tidak ada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui sequence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset nomor sequence ke 0
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset($id)
    {
        try {
            $sequence = Sequence::findOrFail($id);
            
            $sequence->update([
                'current_number' => 0,
                'last_reset_date' => now()->toDateString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor sequence berhasil direset',
                'result' => $sequence->fresh()
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sequence tidak ditemukan',
                'error' => 'Sequence dengan ID ' . $id . ' tidak ada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset sequence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview format sequence berdasarkan parameter yang diberikan
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'prefix' => 'required|string|max:50',
                'date_format' => 'nullable|string|in:dmy,ymd,ym,my',
                'padding' => 'required|integer|min:1|max:10',
                'current_number' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $prefix = $request->prefix;
            $dateFormat = $request->date_format ?: 'ymd';
            $padding = $request->padding;
            $currentNumber = $request->current_number ?: 1;

            // Format tanggal sesuai format yang dipilih
            $today = now();
            $dateFormatMap = [
                'dmy' => $today->format('dmy'),
                'ymd' => $today->format('ymd'),
                'ym' => $today->format('ym'),
                'my' => $today->format('my')
            ];
            
            $datePart = $dateFormatMap[$dateFormat] ?? $today->format('ymd');
            $number = str_pad($currentNumber, $padding, '0', STR_PAD_LEFT);
            
            $preview = "{$prefix}/{$datePart}/{$number}";

            return response()->json([
                'success' => true,
                'result' => [
                    'preview' => $preview,
                    'components' => [
                        'prefix' => $prefix,
                        'date_part' => $datePart,
                        'number_part' => $number
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat preview',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}