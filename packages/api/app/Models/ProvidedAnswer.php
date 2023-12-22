<?php

namespace App\Models;

use App\Casts\Json;
use App\Enums\AnswerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProvidedAnswer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'answer_id',
        'property_id',
        'active_form_id',
        'value',
    ];

    protected $casts = [
        'value' => Json::class,
    ];

    public function getValueAttribute()
    {
        if ($this->attributes['value'] === null) {
            return null;
        }

        $value = json_decode($this->attributes['value'], true);

        if ($this->answer?->type === AnswerType::File) {
            if (is_null($this->answer->step->repeatable_answer_id)) {
                if ($value === 'Not applicable') {
                    return 'Not applicable';
                }

                if ($value === 'Add later') {
                    return 'Add later';
                }

                $file = $this->fileValue;

                if (! $file) {
                    return $value;
                }

                return [
                    'id' => $file->id,
                    'url' => $file->getUrl(),
                    'name' => $file->name,
                    'custom_properties' => $file->custom_properties,
                ];
            } else {
                return collect($value)->map(function ($valueItem, $index) {
                    if (in_array($valueItem, ['Not applicable', 'Add later'])) {
                        return $valueItem;
                    }

                    $file = $this->getFirstMedia('file_value', function (Media $media) use ($index) {
                        return $media->getCustomProperty('repeatable_index') === $index;
                    });

                    if (! $file) {
                        return $valueItem;
                    }

                    return [
                        'id' => $file->id,
                        'url' => $file->getUrl(),
                        'name' => $file->name,
                        'custom_properties' => $file->custom_properties,
                    ];
                })
                ->toArray();
            }
        }

        return $value;
    }

    /**
     * Get the profile image
     */
    public function getFileValueAttribute(): Media|MediaCollection|null
    {
        return is_null($this->answer->step->repeatable_answer_id)
            ? $this->getFirstMedia('file_value')
            : $this->getMedia('file_value');
    }

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file_value');
    }

    /**
     * Answer relationship
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Property relationship
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
