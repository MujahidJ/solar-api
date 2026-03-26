<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConditionEvent extends Model
{
    protected $fillable = [
        'installation_id',
        'technician_id',
        'event_type',
        'notes',
    ];


    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
