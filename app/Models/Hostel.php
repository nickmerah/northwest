<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $table = 'hostels';
    protected $primaryKey = 'hid';

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'hostelid', 'hid');
    }

    public function ofee()
    {
        return $this->belongsTo(OFee::class, 'related_ofee_id', 'of_id');
    }
}
