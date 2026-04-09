<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\ConditionEventType;


class ConditionEvent extends Model
{
    protected $fillable = [
        'installation_id',
        'technician_id',
        'event_type',
        'notes',
    ];

    protected $casts = [
    'event_type' => ConditionEventType::class,
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
