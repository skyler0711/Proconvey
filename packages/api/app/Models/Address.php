<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_1',
        'line_2',
        'city',
        'postcode',
        'uprn',
    ];

    /**
     * Get the single line address
     */
    public function getSingleLineAttribute(): string
    {
        return implode(', ', array_filter([
            $this->line_1,
            $this->line_2,
            $this->city,
            $this->postcode,
            $this->uprn,
        ]));
    }

    /**
     * Get the addressable model
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
