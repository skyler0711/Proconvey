<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Form extends Model implements HasMedia
{
    use HasFactory, HasRelationships, InteractsWithMedia;

    protected $fillable = [
        'name',
        'ta_form_template',
        'order_number',
    ];

    protected $casts = [
        'signature_coords' => 'array',
        'current_date_field' => 'array',
    ];

    /**
     * Get the image
     */
    public function getImageAttribute(): ?Media
    {
        return $this->getFirstMedia('image');
    }

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    /**
     * Sections relationship
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Answers relationship
     */
    public function answers(): HasManyThrough
    {
        return $this
            ->hasManyDeep(
                Answer::class,
                [Section::class, Step::class],
                ['form_id', 'section_id', 'step_id'],
                ['id', 'id', 'id'],
            );
    }

    /**
     * Signed Properties relationship
     */
    public function signedProperties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'form_property')
            ->withPivot('letters_envelope_id', 'letters_envelope_token');
    }

    /**
     * Conditions relationship
     */
    public function conditions(): MorphMany
    {
        return $this->morphMany(Condition::class, 'conditionable');
    }

    /**
     * Repeatable answer relationship
     */
    public function repeatableAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Template relationship
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Step::class, 'template_id');
    }

    /**
     * Active forms properties relationship
     */
    public function properties(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Property::class,
                'active_forms',
                'form_id',
                'property_id',
            )
            ->withPivot(
                'id',
                'title',
            );
    }
}
