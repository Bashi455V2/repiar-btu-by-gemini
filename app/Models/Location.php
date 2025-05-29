<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building',
        'floor',
        'room_number',
        'details',
    ];

    /**
     * Get all of the repair requests for the Location
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class, 'location_id');
    }
}