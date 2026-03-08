<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    protected $fillable = [
        'installation_id',
        'title',
        'trigger_type',
        'interval_days',
        'condition_rule',
        'next_due_date',
        'active',
    ];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
