<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonies extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'name',
        'business_name',
        'description',
    ];
}
