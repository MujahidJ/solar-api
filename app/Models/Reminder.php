<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
  protected $fillable = [
    'installation_id',
    'maintenance_plan_id',
    'client_id',
    'technician_id',
    'message',
    'due_date',
    'status',
    'trigger_source',
];

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function maintenancePlan()
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
