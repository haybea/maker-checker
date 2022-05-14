<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRequest extends Model
{
    use HasFactory;
    protected $hidden = [
        'created_at', 'updated_at', 'maker_id','checker_id'
    ];
    protected $casts = [
        'payload' => 'array'
    ];

    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value);
    }
}
