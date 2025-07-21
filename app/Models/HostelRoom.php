<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $table = 'hostelroom';
    protected $primaryKey = 'roomid';

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostelid', 'hid');
    }

    public function allocations()
    {
        return $this->hasMany(HostelRoomAllocation::class, 'room_id', 'roomid');
    }

    // Check if room has available space
    public function hasSpace()
    {
        return $this->allocations()->count() < $this->capacity;
    }
}
