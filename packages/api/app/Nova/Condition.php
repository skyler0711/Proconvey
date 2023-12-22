<?php

namespace App\Nova;

use App\Enums\AnswerType;
use App\Models\Answer as AnswerModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Condition extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Condition>
     */
    public static $model = \App\Models\Condition::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            MorphTo::make('Conditionable')
                ->types([
                    Answer::class,
                    Step::class,
                ])
                ->rules('required'),

            BelongsTo::make('Answer')
                ->relatableQueryUsing(function ($request, $query) {
                    return $query->whereIn('type', AnswerType::getSelectableTypes());
                })
                ->displayUsing(function ($answer) {
                    return "{$answer->resource->step->question} - {$answer->title()}";
                })
                ->rules('required'),

            Select::make('Selected Value')
                ->hide()
                ->dependsOn(['answer'], function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->answer) {
                        $field
                            ->show()
                            ->options(
                                collect(AnswerModel::find($formData->answer)->getDetails()->options)->pluck('value', 'value')
                            )
                            ->rules('required', 'max:254');
                    }
                }),
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
