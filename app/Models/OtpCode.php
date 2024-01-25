<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class OtpCode extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'expires_at'];

    protected $dates = ['expires_at'];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Register an event listener for the "deleting" event
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function ($otpCode) {
    //         // Delete the OTP code if it has already expired
    //         if ($otpCode->expires_at <= now()) {
    //             $otpCode->forceDelete(); // Use forceDelete to permanently delete the row
    //         }
    //     });
    // }
}
