<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'symbol',
        'name',
    ];

    public function adverts(): HasMany
    {
        return $this->hasMany(Advert::class);
    }

    public function getFormattedPriceAttribute($price): string
    {
        return "{$this->symbol}{$price}";
    }
}
