<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conveyancer_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'job_role',
        'occupation',
        'title',
        'suffix',
        'email_verified_at',
        'case_reference',
        'terms_and_conditions',
        'id_check',
        'payment_on_account',
        'phone',
        'job_bio',
        'invite_code',
        'invite_code_sent_at',
        'business_created_at',
        'sra_clc_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the profile image
     */
    public function getProfileImageAttribute(): ?Media
    {
        return $this->getFirstMedia('profile_image');
    }

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')->singleFile();
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        $data = [];
        if ($this->title) {
            $data[] = Str::title($this->title);
        }
        $data = [
            ...$data,
            $this->first_name,
            $this->last_name,
        ];
        if ($this->suffix) {
            $data[] = $this->suffix;
        }

        return implode(' ', $data);
    }

    /**
     * Conveyancer relationship
     */
    public function conveyancer(): BelongsTo
    {
        return $this->belongsTo(Conveyancer::class);
    }

    /**
     * Address relationship
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Properties relationship
     */
    public function properties(): BelongsToMany
    {
        return $this
            ->belongsToMany(Property::class)
            ->withPivot(
                'role',
                'is_primary_user',
                'onboarding_forms_completed_at',
                'payment_on_account_completed_at',
                'sof_completed_at',
                'representation',
            )
            ->using(PropertyUser::class);
    }

    /**
     * Conveyancer Users relationship
     */
    public function idVerification(): HasMany
    {
        return $this->hasMany(IdVerification::class);
    }

    /**
     * Notification Preferences relationship
     */
    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    public function newNotifications()
    {
        return $this->unreadNotifications()->limit(20);
    }
}
