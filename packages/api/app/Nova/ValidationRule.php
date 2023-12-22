<?php

namespace App\Nova;

use App\Enums\ValidationRules;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ValidationRule extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ValidationRule>
     */
    public static $model = \App\Models\ValidationRule::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'rule';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'rule',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('Answer'),

            Select::make('Rule')
                ->options(ValidationRules::asSelectArray())
                ->displayUsingLabels()
                ->rules('required'),
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
