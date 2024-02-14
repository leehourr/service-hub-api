<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = "bookings";
    protected $fillable = [
        'date_time',
        'status',
        'created_at',
        'updated_at',
        'user_id',
        'service_provider_id',
        'service_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceListing::class, 'service_id');
    }
}
