<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'service_description',
        'service_category',
        'pricing',
        'created_at',
        'updated_at',
        'service_provider_id',
        'status'
    ];
    
    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }
}
