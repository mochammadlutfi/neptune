<?php
// app/Models/DVRUpload.php

namespace App\Models\DVRUpload;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DVRUpload extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'vessel_id',
        'date',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'detected_sheets',
        'detected_buyers',
        'structure_info',
        'status',
        'total_transactions',
        'imported_transactions',
        'failed_transactions',
    ];
    
    protected $casts = [
        'date' => 'date',
        'detected_sheets' => 'array',
        'detected_buyers' => 'array',
        'structure_info' => 'array',
        'uploaded_at' => 'datetime',
    ];
    
    // Relationships
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }
    
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    public function transactions()
    {
        return $this->hasMany(DVRImportTransaction::class);
    }
    
    // Scopes
    public function scopeForVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }
    
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
    
    // Accessors
    public function getFullPathAttribute()
    {
        return Storage::path($this->file_path);
    }
    
    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
    
    // Methods
    public function isPending(): bool
    {
        return $this->status === 'uploaded';
    }
    
    public function isCompleted(): bool
    {
        return $this->imported_transactions === $this->total_transactions;
    }
    
    public function getCompletionPercentage(): float
    {
        if ($this->total_transactions === 0) return 0;
        return round(($this->imported_transactions / $this->total_transactions) * 100, 2);
    }
    
    public function canBeImported(): bool
    {
        return in_array($this->status, ['uploaded', 'processing', 'failed']);
    }
    
    // Delete file when model deleted
    protected static function booted()
    {
        static::deleted(function ($upload) {
            if (Storage::exists($upload->file_path)) {
                Storage::delete($upload->file_path);
            }
        });
    }
}