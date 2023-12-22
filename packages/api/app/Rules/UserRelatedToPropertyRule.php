<?php

namespace App\Rules;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRelatedToPropertyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = Auth::user();

        $userExistsOnPivot = DB::table('property_user')->where('user_id', $user->id)->where('property_id', $value)->exists();

        if ($userExistsOnPivot) {
            return true;
        }

        if ($user->role === UserRole::Conveyancer) {
            $conveyancerLinkedToPropertyId = $user->conveyancer->properties()->where('id', $value)->exists();

            if ($conveyancerLinkedToPropertyId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Property id is invalid';
    }
}
