<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Settings\CompanyService;
use App\Http\Requests\Settings\CompanyFormRequest;

class CompanySettingController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = $this->companyService->getCompanies($request->all());
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyFormRequest $request)
    {
        try {
            $company = $this->companyService->createCompany($request->validated());
            return response()->json(['success' => true, 'result' => $company], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = $this->companyService->findCompany($id);
            if (!$company) {
                return response()->json(['message' => 'Company not found.'], 404);
            }
            return response()->json(['success' => true, 'result' => $company]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyFormRequest $request, string $id)
    {
        try {
            $company = $this->companyService->updateCompany($id, $request->validated());
            return response()->json(['success' => true, 'result' => $company]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->companyService->deleteCompany($id);
            return response()->json(['success' => true], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get company stats
     */
    public function stats()
    {
        $stats = $this->companyService->getCompanyStats();
        return response()->json(['result' => $stats]);
    }

    /**
     * Bulk delete companies
     */
    public function bulkDelete(Request $request)
    {
        try {
            $this->companyService->bulkDeleteCompanies($request->ids);
            return response()->json(['success' => true], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get company settings
     */
    public function getSettings(string $id)
    {
        try {
            $settings = $this->companyService->getCompanySettings($id);
            return response()->json(['success' => true, 'result' => $settings]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update company settings
     */
    public function updateSettings(Request $request, string $id)
    {
        try {
            $settings = $this->companyService->updateCompanySettings($id, $request->all());
            return response()->json(['success' => true, 'result' => $settings]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
