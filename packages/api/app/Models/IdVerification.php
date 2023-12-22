<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class IdVerification extends Model
{
    protected $table = 'id_verifications';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'conveyancer_id',
        'session_id',
        'client_token',
        'mobile_connected_at',
        'id_verification_completed_at',
    ];

    /**
     * User Relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Conveyancer Relationship
     */
    public function conveyancer(): BelongsTo
    {
        return $this->belongsTo(Conveyancer::class, 'conveyancer_id');
    }

    /**
     * Property Relationship
     */
    public function property(): HasManyThrough
    {
        return $this->hasManyThrough(
            Property::class,
            PropertyUser::class,
            'id_verification_id',
            'id',
            'id',
            'property_id'
        );
    }
}
