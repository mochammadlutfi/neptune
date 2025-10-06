<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MailTemplate;

class MailTemplateController extends Controller
{
    /**
     * Menampilkan daftar template email
     */
    public function index()
    {
        try {
            $templates = MailTemplate::orderBy('name')->get();
            
            return response()->json([
                'success' => true,
                'data' => $templates,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load email templates: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan template email berdasarkan ID
     */
    public function show($id)
    {
        try {
            $template = MailTemplate::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $template,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Menyimpan template email baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:100|unique:mail_templates,code',
            'name' => 'required|string|max:150|unique:mail_templates,name',
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $template = MailTemplate::create([
                'code' => $request->code,
                'name' => $request->name,
                'subject' => $request->subject,
                'body' => $request->body,
                'description' => $request->description,
                'variables' => json_encode($request->variables ?? []),
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Email template created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create email template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memperbarui template email
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:100|unique:mail_templates,code,' . $id,
            'name' => 'required|string|max:150|unique:mail_templates,name,' . $id,
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $template = MailTemplate::findOrFail($id);
            
            $template->update([
                'code' => $request->code,
                'name' => $request->name,
                'subject' => $request->subject,
                'body' => $request->body,
                'description' => $request->description,
                'variables' => json_encode($request->variables ?? []),
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Email template updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus template email
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $template = MailTemplate::findOrFail($id);
            $template->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Email template deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete email template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan template berdasarkan kode
     */
    public function byCode($code)
    {
        try {
            $template = MailTemplate::where('code', $code)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $template,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Preview template dengan data sample
     */
    public function preview(Request $request, $id)
    {
        try {
            $template = MailTemplate::findOrFail($id);
            $sampleData = $request->input('data', []);
            
            // Replace variables dalam template
            $subject = $this->replaceVariables($template->subject, $sampleData);
            $body = $this->replaceVariables($template->body, $sampleData);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'subject' => $subject,
                    'body' => $body,
                    'type' => $template->type,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to preview template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate template
     */
    public function duplicate($id)
    {
        DB::beginTransaction();
        try {
            $originalTemplate = MailTemplate::findOrFail($id);
            
            $newTemplate = $originalTemplate->replicate();
            $newTemplate->name = $originalTemplate->name . ' (Copy)';
            $newTemplate->is_active = false;
            $newTemplate->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $newTemplate,
                'message' => 'Email template duplicated successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan template
     */
    public function toggleStatus($id)
    {
        DB::beginTransaction();
        try {
            $template = MailTemplate::findOrFail($id);
            $template->is_active = !$template->is_active;
            $template->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Template status updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper function untuk mengganti variables dalam template
     */
    private function replaceVariables($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Mendapatkan daftar kode template yang tersedia
     */
    public function getCodes()
    {
        try {
            $codes = MailTemplate::select('code', 'name')
                ->where('is_active', true)
                ->orderBy('code')
                ->get()
                ->pluck('name', 'code');

            return response()->json([
                'success' => true,
                'data' => $codes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kode template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar variables yang tersedia untuk template
     */
    public function getAvailableVariables()
    {
        $variables = [
            'user' => [
                'name' => 'User Name',
                'email' => 'User Email',
                'phone' => 'User Phone',
            ],
            'company' => [
                'name' => 'Company Name',
                'email' => 'Company Email',
                'phone' => 'Company Phone',
                'address' => 'Company Address',
            ],
            'system' => [
                'app_name' => 'Application Name',
                'app_url' => 'Application URL',
                'current_date' => 'Current Date',
                'current_time' => 'Current Time',
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $variables,
        ], 200);
    }
}