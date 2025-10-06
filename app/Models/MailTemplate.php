<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel dalam database
     */
    protected $table = 'mail_templates';

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'code',
        'name',
        'subject',
        'body',
        'description',
        'variables',
        'is_active',
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [];

    /**
     * Scope untuk template yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk template berdasarkan kode
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Accessor untuk mendapatkan variables sebagai array
     */
    public function getVariablesArrayAttribute()
    {
        return is_string($this->variables) ? json_decode($this->variables, true) : $this->variables;
    }

    /**
     * Mutator untuk menyimpan variables sebagai JSON
     */
    public function setVariablesAttribute($value)
    {
        $this->attributes['variables'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Method untuk mengganti variables dalam template
     */
    public function render($data = [])
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Method untuk mendapatkan preview template
     */
    public function preview($sampleData = [])
    {
        // Data sample default jika tidak ada yang diberikan
        $defaultData = [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
            'company_name' => 'Your Company',
            'app_name' => 'ERP System',
            'current_date' => now()->format('Y-m-d'),
            'current_time' => now()->format('H:i:s'),
        ];

        $data = array_merge($defaultData, $sampleData);
        return $this->render($data);
    }

    /**
     * Method untuk mendapatkan daftar variables yang digunakan dalam template
     */
    public function getUsedVariables()
    {
        $content = $this->subject . ' ' . $this->body;
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
        
        return array_unique($matches[1]);
    }

    /**
     * Method untuk validasi template
     */
    public function validate()
    {
        $errors = [];

        // Validasi subject tidak kosong
        if (empty($this->subject)) {
            $errors[] = 'Subject is required';
        }

        // Validasi body tidak kosong
        if (empty($this->body)) {
            $errors[] = 'Body is required';
        }

        // Validasi kode template
        if (empty($this->code)) {
            $errors[] = 'Code is required';
        }

        // Validasi variables format
        if (!empty($this->variables) && !is_array($this->variables_array)) {
            $errors[] = 'Variables must be in valid JSON format';
        }

        return $errors;
    }

    /**
     * Method untuk clone template
     */
    public function duplicate($newName = null)
    {
        $clone = $this->replicate();
        $clone->name = $newName ?: $this->name . ' (Copy)';
        $clone->is_active = false;
        $clone->save();

        return $clone;
    }

    /**
     * Boot method untuk model events
     */
    protected static function boot()
    {
        parent::boot();

        // Event saat template dibuat
        static::creating(function ($template) {
            // Set default values jika belum ada
            if (is_null($template->is_active)) {
                $template->is_active = true;
            }
            
            // Set default code jika belum ada
            if (empty($template->code)) {
                $template->code = strtolower(str_replace(' ', '_', $template->name));
            }
        });

        // Event saat template diupdate
        static::updating(function ($template) {
            // Log perubahan jika diperlukan
            // Bisa ditambahkan logging di sini
        });
    }
}