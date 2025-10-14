<?php
// app/Models/DVRImportTransaction.php

namespace App\Models\DVRUpload;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class DVRImportTransaction extends Model
{
    protected $fillable = [
        'dvr_upload_id',
        'transaction_type',
        'transaction_name',
        'sheet_name',
        'status',
        'started_at',
        'completed_at',
        'processed_by',
        'records_imported',
        'records_skipped',
        'records_failed',
        'success_rate',
        'error_message',
        'error_details',
        'import_options',
        'processing_time_seconds',
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'error_details' => 'array',
        'import_options' => 'array',
    ];
    
    // Relationships
    public function upload()
    {
        return $this->belongsTo(DVRUpload::class, 'dvr_upload_id');
    }
    
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    public function logs()
    {
        return $this->hasMany(DVRImportLog::class);
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    // Methods
    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
            'processed_by' => auth()->id(),
        ]);
    }
    
    public function markAsCompleted(int $imported, int $skipped = 0)
    {
        $total = $imported + $skipped;
        $successRate = $total > 0 ? ($imported / $total) * 100 : 0;
        
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'records_imported' => $imported,
            'records_skipped' => $skipped,
            'success_rate' => $successRate,
            'processing_time_seconds' => $this->started_at ? 
                now()->diffInSeconds($this->started_at) : 0,
        ]);
        
        // Update parent upload
        $this->upload->increment('imported_transactions');
        $this->upload->updateStatus();
    }
    
    public function markAsFailed(string $error, array $details = [])
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $error,
            'error_details' => $details,
            'processing_time_seconds' => $this->started_at ? 
                now()->diffInSeconds($this->started_at) : 0,
        ]);
        
        // Update parent upload
        $this->upload->increment('failed_transactions');
        $this->upload->updateStatus();
    }
    
    public function canBeImported(): bool
    {
        return in_array($this->status, ['pending', 'failed']);
    }
    
    public function canBeReImported(): bool
    {
        return $this->status === 'failed';
    }
}