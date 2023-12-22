<?php

namespace App\Nova;

use App\Enums\AnswerType;
use App\Enums\StepType;
use App\Models\Answer as AnswerModel;
use App\Models\Step as StepModel;
use BenSampo\Enum\Rules\EnumValue;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Step extends Resource
{
    use HasSortableRows;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Step>
     */
    public static $model = \App\Models\Step::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'question';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'question',
    ];

    /**
     * The default ordering for the resource.
     */
    public static $orderBy = [
        'created_at' => 'asc',
    ];

    /**
     * Allows the user to sort the steps in the form
     */
    public static $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
        'nova_order_by' => 'ASC',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('Section'),

            Text::make('Question')
                ->rules('required', 'max:254'),

            Textarea::make('Sub-heading', 'sub_heading')
                ->nullable()
                ->alwaysShow(),

            Select::make('Type')
                ->rules('required', new EnumValue(StepType::class))
                ->options(StepType::asSelectArray())
                ->displayUsingLabels(),

            Boolean::make('Repeatable')
                ->hide()
                ->help('Should the answers in this step be repeated based on a previous answer?')
                ->onlyOnForms()
                ->dependsOn('type', function (Boolean $field, NovaRequest $request, FormData $formData) {
                    if ($formData->get('type') === StepType::Custom) {
                        $field->show();
                    }
                })
                ->fillUsing(function (NovaRequest $request, StepModel $model, string $attribute, string $requestAttribute) {
                    unset($model[$requestAttribute]);
                })
                ->resolveUsing(function () {
                    return $this->repeatable_step_id !== null;
                }),

            Images::make('Image', 'image')
                ->rules(['required', 'max:5120'])
                ->uploadsToVapor(App::isProduction())
                ->hideFromIndex(),

            Select::make('Repeatable Answer', 'repeatable_answer_id')
                ->help('Which numeric answer determines how many times this step is repeated?')
                ->hide()
                ->nullable()
                ->dependsOn('repeatable', function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->get('repeatable')) {
                        $field
                            ->show()
                            ->rules('required')
                            ->options(
                                AnswerModel::query()
                                    ->with('step.section.form')
                                    ->where('type', AnswerType::Number)
                                    ->orWhere(function ($q) {
                                        return $q
                                            ->where('type', AnswerType::SingleSelect)
                                            ->whereRaw('details->\'$.options[*].value\' regexp \'^[0-9\\\[\\\]\\\",\\\s]+$\'');
                                    })
                                    ->get()
                                    ->map(fn ($answer) => [
                                        'id' => $answer->id,
                                        'value' => $answer->step->section->form->name.' - '.$answer->step->question.' - '.$answer->type,
                                    ])
                                    ->pluck('value', 'id')
                            )
                            ->displayUsingLabels();
                    }
                })
                ->hideFromIndex()
                ->hideFromDetail(function () {
                    return $this->repeatable_answer_id === null;
                })
                ->displayUsing(function ($value) {
                    $answer = AnswerModel::where('id', $value)->with('step.section.form')->first();

                    return $answer->step->section->form->name.' - '.$answer->step->question.' - '.$answer->type;
                }),

            Panel::make('Help Details', [
                Trix::make('Help Text')
                    ->alwaysShow()
                    ->hideFromIndex(),
                URL::make('Help Video Link')
                    ->hideFromIndex(),
            ]),

            HasMany::make('Answers')
                ->hideFromDetail(fn () => $this->type !== StepType::Custom),

            MorphMany::make('Conditions', 'conditions', Condition::class)
                ->hideFromDetail(fn () => $this->type !== StepType::Custom),
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
