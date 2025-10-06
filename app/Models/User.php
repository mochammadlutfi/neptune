<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'status',
        'vessel_id',
    ];

    protected $appends = [
        // 'image_url',
        'status',
    ];

    // public function getImageUrlAttribute()
    // {
    //     if ($this->attributes['image']) {
    //         return Storage::disk('public')->url($this->attributes['image']);
    //     } else {
    //         return null;
    //     }
    // }

    public function getStatusAttribute()
    {
        // Assuming a default status or a column named 'is_active'
        // You might need to adjust this based on your actual user status logic
        return $this->attributes['status'] ?? 'active'; // Default to active if no status column
    }


    /**
     * Relasi dengan vessel yang sedang aktif untuk user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(\App\Models\Master\Vessel::class, 'vessel_id');
    }

    public function vessels(){
        return $this->belongsToMany(\App\Models\Master\Vessel::class,'user_vessels', 'user_id', 'vessel_id');  
    }
    /**
     * Mendapatkan semua vessel yang dapat diakses user
     * Untuk saat ini return semua vessel aktif, nanti bisa dikustomisasi berdasarkan role
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessibleVessels()
    {
        return \App\Models\Master\Vessel::where('status', 'Active');
    }
}
