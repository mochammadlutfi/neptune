<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceActivitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipment_id' => 'nullable|exists:equipment,id',
            'activity_date' => 'required|date',
            'work_order_no' => 'nullable|string|max:50',
            'work_type' => [
                'required',
                'string',
                Rule::in(['Preventive', 'Corrective', 'Breakdown', 'Inspection', 'Calibration'])
            ],
            'description' => 'required|string|max:1000',
            'work_hours' => 'nullable|numeric|min:0|max:999.99',
            'manpower_count' => 'nullable|integer|min:1|max:50',
            'status' => [
                'nullable',
                'string',
                Rule::in(['Planned', 'In Progress', 'Completed', 'Deferred', 'Cancelled'])
            ],
            'completed_by' => 'nullable|string|max:100',
            'completion_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.exists' => 'Equipment yang dipilih tidak valid.',
            'activity_date.required' => 'Tanggal aktivitas wajib diisi.',
            'activity_date.date' => 'Format tanggal aktivitas tidak valid.',
            'work_order_no.max' => 'Work order number maksimal 50 karakter.',
            'work_type.required' => 'Jenis pekerjaan wajib dipilih.',
            'work_type.in' => 'Jenis pekerjaan tidak valid.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'description.max' => 'Deskripsi pekerjaan maksimal 1000 karakter.',
            'work_hours.min' => 'Work hours tidak boleh negatif.',
            'work_hours.max' => 'Work hours terlalu besar.',
            'manpower_count.min' => 'Jumlah manpower minimal 1.',
            'manpower_count.max' => 'Jumlah manpower maksimal 50.',
            'status.in' => 'Status tidak valid.',
            'completed_by.max' => 'Completed by maksimal 100 karakter.',
            'completion_date.date' => 'Format tanggal penyelesaian tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi completion_date tidak boleh sebelum activity_date
            $activityDate = $this->input('activity_date');
            $completionDate = $this->input('completion_date');

            if ($activityDate && $completionDate && $completionDate < $activityDate) {
                $validator->errors()->add('completion_date', 'Tanggal penyelesaian tidak boleh sebelum tanggal aktivitas.');
            }

            // Validasi jika status Completed, completion_date dan completed_by harus diisi
            $status = $this->input('status');
            $completedBy = $this->input('completed_by');

            if ($status === 'Completed') {
                if (!$completionDate) {
                    $validator->errors()->add('completion_date', 'Tanggal penyelesaian wajib diisi untuk status Completed.');
                }
                if (!$completedBy) {
                    $validator->errors()->add('completed_by', 'Completed by wajib diisi untuk status Completed.');
                }
            }
        });
    }
}