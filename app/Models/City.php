<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'KM'
    ];

    public function service()
    {
        return $this->hasOne(Service::class);
    }

}