<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Along extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'along_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}