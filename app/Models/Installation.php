<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'location',
        'installation_date',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(
            User::class,
            'installation_technician',
            'installation_id',
            'technician_id'
        )->withTimestamps()->withPivot('assigned_at');
    }

    public function maintenancePlans()
    {
        return $this->hasMany(MaintenancePlan::class);
    }

    public function serviceVisits()
    {
        return $this->hasMany(ServiceVisit::class);
    }
}