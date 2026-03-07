<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    protected $fillable = [
        'installation_id',
        'name',
        'description',
        'frequency',
        'next_due_date',
    ];
    public function installation()
{
    return $this->belongsTo(Installation::class);
}
}
