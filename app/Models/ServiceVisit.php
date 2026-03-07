<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceVisit extends Model
{
    protected $fillable = [
        'installation_id',
        'technician_id',
        'serviced_on',
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
