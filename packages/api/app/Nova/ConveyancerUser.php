<?php

namespace App\Nova;

use App\Enums\UserJobRole;
use App\Enums\UserRole;
use App\Enums\UserTitle;
use BenSampo\Enum\Rules\EnumValue;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Greg0x46\MaskedField\MaskedField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class ConveyancerUser extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ConveyancerUser>
     */
    public static $model = \App\Models\ConveyancerUser::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'first_name', 'last_name', 'email',
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Conveyancers';
    }

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Users';

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $query = parent::indexQuery($request, $query);

        return $query->where('role', UserRole::Conveyancer);
    }

    /**
     * Fill the given fields for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Support\Collection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        $model = parent::fillFields($request, $model, $fields);
        $model[0]->role = UserRole::Conveyancer;

        return $model;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Images::make('Profile Image', 'profile_image')
                ->onlyOnIndex(),

            Select::make('Title')
                ->options(UserTitle::asSelectArray())
                ->displayUsingLabels()
                ->rules(new EnumValue(UserTitle::class))
                ->onlyOnForms(),

            Text::make('First Name')
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make('Last Name')
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make('Name', 'full_name')
                ->exceptOnForms(),

            Stack::make('Contact', [
                Text::make('Email'),
                Text::make('Phone'),
            ]),

            Text::make('Email')
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->onlyOnForms(),

            MaskedField::make('Phone')
                ->mask('+44#### ######')
                ->rules('required', 'max:254')
                ->onlyOnForms(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),

            BelongsTo::make('Conveyancing Firm', 'conveyancer', Conveyancer::class)
                ->nullable(),

            Panel::make('Profile', [
                Select::make('Job Role')
                    ->options(UserJobRole::asSelectArray())
                    ->displayUsingLabels()
                    ->rules(['required', new EnumValue(UserJobRole::class)])
                    ->hideFromIndex(),

                Textarea::make('Job Bio')
                    ->rules('required')
                    ->alwaysShow()
                    ->hideFromIndex(),

                Images::make('Profile Image', 'profile_image')
                    ->rules('max:5120')
                    ->uploadsToVapor()
                    ->hideFromIndex(),
            ]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
