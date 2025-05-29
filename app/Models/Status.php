<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color_class', // สำหรับ UI
    ];

    /**
     * Get all of the repair requests for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class, 'status_id');
    }
}