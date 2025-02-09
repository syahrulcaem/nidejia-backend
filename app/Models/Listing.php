<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'address',
        'sqtf',
        'wife_speed',
        'max_person',
        'price_per_day',
        'attachments',
        'full_support_available',
        'gym_area_available',
        'mini_cafe_available',
        'cinema_available',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    function getRouteKeyName()
    {
        return 'slug';
    }

    function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
