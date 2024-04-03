<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;
    
    protected $table = 'otps';

    protected $fillable = [
        'code',
        'account_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($otp) {
            $otp->expires_at = now()->addMinutes(2);
        });
    }
    
}
