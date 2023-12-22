<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PropertyUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_primary_user' => 'boolean',
    ];

    /**
     * ID Verification relationship
     */
    public function idVerification(): HasOne
    {
        return $this->hasOne(IdVerification::class, 'id_verification_id');
    }
}
