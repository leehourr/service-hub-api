<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'expires_at'];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Register an event listener for the "deleting" event
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($otpCode) {
            // Delete the OTP code if it has already expired
            if ($otpCode->expires_at <= now()) {
                $otpCode->forceDelete(); // Use forceDelete to permanently delete the row
            }
        });
    }
}
