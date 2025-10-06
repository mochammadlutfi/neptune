<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\ContractRequest;
use App\Http\Resources\Master\ContractResource;
use App\Models\Master\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar kontrak dengan filter dan pencarian.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contract::query();

        // Apply filters
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('contract_status')) {
            $query->where('contract_status', $request->contract_status);
        }

        if ($request->filled('operator_name')) {
            $query->where('operator_name', 'LIKE', '%' . $request->operator_name . '%');
        }

        if ($request->filled('field_name')) {
            $query->where('field_name', 'LIKE', '%' . $request->field_name . '%');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'LIKE', '%' . $search . '%')
                  ->orWhere('contract_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('field_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('operator_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('block_name', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['contract_number', 'contract_name', 'contract_type', 'operator_name', 'effective_date', 'expiry_date', 'contract_status'];
        $sort = $request->get('sort', 'contract_number');
        $sortDir = $request->get('sortDir', 'asc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('contract_number', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100); // Batasi maksimal 100
        
        if ($request->filled('page')) {
            $contracts = $query->paginate($limit);
            return response()->json([
                'data' => ContractResource::collection($contracts->items()),
                'meta' => [
                    'current_page' => $contracts->currentPage(),
                    'last_page' => $contracts->lastPage(),
                    'per_page' => $contracts->perPage(),
                    'total' => $contracts->total(),
                    'from' => $contracts->firstItem(),
                    'to' => $contracts->lastItem(),
                ]
            ]);
        } else {
            $contracts = $query->get();
            return response()->json([
                'data' => ContractResource::collection($contracts)
            ]);
        }
    }

    /**
     * Menyimpan kontrak baru ke database.
     */
    public function store(ContractRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            // Konversi partner_companies jika ada
            if (isset($validatedData['partner_companies']) && is_array($validatedData['partner_companies'])) {
                $validatedData['partner_companies'] = array_filter($validatedData['partner_companies']);
            }
            
            $contract = Contract::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil dibuat.',
                'data' => new ContractResource($contract)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kontrak: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menampilkan detail kontrak berdasarkan ID.
     */
    public function show(String $id): JsonResponse
    {
        // Load relationships jika diperlukan
        // $contract->loadCount(['contractTargets', 'dailyPerformances']);
        $data = Contract::where('id', $id)->first();
        return response()->json([
            'success' => true,
            'data' => new ContractResource($data)
        ]);
    }

    /**
     * Mengupdate data kontrak.
     */
    public function update(ContractRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            // Konversi partner_companies jika ada
            if (isset($validatedData['partner_companies']) && is_array($validatedData['partner_companies'])) {
                $validatedData['partner_companies'] = array_filter($validatedData['partner_companies']);
            }
            
            $contract->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil diupdate.',
                'data' => new ContractResource($contract->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kontrak: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menghapus kontrak dari database.
     */
    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Cek dependensi sebelum penghapusan
            $targetsCount = $contract->contractTargets()->count();
            $performancesCount = $contract->dailyPerformances()->count();
            
            if ($targetsCount > 0 || $performancesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus kontrak yang memiliki {$targetsCount} target dan {$performancesCount} record performa.",
                ], 422);
            }

            $contractName = $contract->contract_name;
            $contract->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Kontrak '{$contractName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kontrak: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mendapatkan performa kontrak untuk tahun tertentu.
     */
    public function getContractPerformance(Contract $contract, Request $request): JsonResponse
    {
        $year = $request->get('year', Carbon::now()->year);
        
        // Validasi tahun
        if (!is_numeric($year) || $year < 2000 || $year > Carbon::now()->addYears(5)->year) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak valid.'
            ], 422);
        }
        
        // Ambil data performa sesuai dengan field model DailyContractPerformance
        $performance = $contract->dailyPerformances()
            ->whereYear('report_date', $year)
            ->selectRaw('
                SUM(oil_produced_bbl) as total_oil_production,
                SUM(gas_produced_mmscf) as total_gas_production,
                SUM(oil_delivered_bbl) as total_oil_delivered,
                SUM(gas_delivered_mmscf) as total_gas_delivered,
                SUM(contractor_share_oil_bbl) as total_contractor_oil_share,
                SUM(contractor_share_gas_mmscf) as total_contractor_gas_share,
                AVG(facility_uptime_pct) as avg_uptime,
                SUM(operating_cost_usd) as total_operating_cost,
                COUNT(*) as total_days
            ')
            ->first();

        $targets = $contract->contractTargets()
            ->where('target_year', $year)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'contract' => new ContractResource($contract),
                'performance' => $performance,
                'targets' => $targets,
                'year' => (int) $year,
            ]
        ]);
    }

    /**
     * Mendapatkan daftar kontrak yang akan berakhir dalam periode tertentu.
     */
    public function getExpiringContracts(Request $request): JsonResponse
    {
        $months = $request->get('months', 6);
        
        // Validasi input months
        if (!is_numeric($months) || $months < 1 || $months > 60) {
            $months = 6;
        }
        
        $expiryDate = Carbon::now()->addMonths($months);
        
        $contracts = Contract::where('contract_status', 'Active')
            ->where('expiry_date', '<=', $expiryDate)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ContractResource::collection($contracts),
            'meta' => [
                'months_ahead' => (int) $months,
                'expiry_date_limit' => $expiryDate->format('Y-m-d'),
                'total_contracts' => $contracts->count()
            ]
        ]);
    }

    /**
     * Validasi commercial terms untuk memastikan total persentase = 100%.
     */
    public function validateCommercialTerms(Request $request): JsonResponse
    {
        $request->validate([
            'contractor_share_oil_pct' => 'nullable|numeric|min:0|max:100',
            'government_share_oil_pct' => 'nullable|numeric|min:0|max:100',
            'contractor_share_gas_pct' => 'nullable|numeric|min:0|max:100',
            'government_share_gas_pct' => 'nullable|numeric|min:0|max:100',
        ]);
        
        $contractorOil = (float) ($request->contractor_share_oil_pct ?? 0);
        $governmentOil = (float) ($request->government_share_oil_pct ?? 0);
        $contractorGas = (float) ($request->contractor_share_gas_pct ?? 0);
        $governmentGas = (float) ($request->government_share_gas_pct ?? 0);
        
        $oilTotal = $contractorOil + $governmentOil;
        $gasTotal = $contractorGas + $governmentGas;
        
        $oilValid = abs($oilTotal - 100) < 0.01; // Toleransi untuk floating point
        $gasValid = abs($gasTotal - 100) < 0.01;
        
        return response()->json([
            'success' => true,
            'data' => [
                'oil_shares_valid' => $oilValid,
                'gas_shares_valid' => $gasValid,
                'oil_total' => round($oilTotal, 2),
                'gas_total' => round($gasTotal, 2),
                'validation_messages' => [
                    'oil' => $oilValid ? 'Valid' : "Total oil share: {$oilTotal}% (harus 100%)",
                    'gas' => $gasValid ? 'Valid' : "Total gas share: {$gasTotal}% (harus 100%)"
                ]
            ]
        ]);
    }
}
