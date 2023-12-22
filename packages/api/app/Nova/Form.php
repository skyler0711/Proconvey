<?php

namespace App\Nova;

use App\Enums\AnswerType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\StepType;
use App\Models\Answer as AnswerModel;
use App\Models\Form as FormModel;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;

class Form extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Form>
     */
    public static $model = \App\Models\Form::class;

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
        'id', 'name',
    ];

    /**
     * The default ordering for the resource.
     */
    public static $orderBy = [
        'created_at' => 'asc',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('Template', 'template', Step::class)
                ->help('Which template should be used for this form?')
                ->nullable()
                ->relatableQueryUsing(function ($request, $query) {
                    return $query->where('type', StepType::OwnerName);
                }),

            Text::make('Name')
                ->dependsOn('template', function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->get('template')) {
                        $field->hide();
                    } else {
                        $field->rules('required', 'max:254');
                    }
                }),

            Select::make('Group', 'group')
                ->options(FormGroup::asSelectArray())
                ->rules('required')
                ->displayUsingLabels(),

            Textarea::make('Description')
                ->rules('required')
                ->hideFromIndex()
                ->alwaysShow(),

            Images::make('Image', 'image')
                ->rules(['required', 'max:5120'])
                ->uploadsToVapor(App::isProduction())
                ->hideFromIndex(),

            Boolean::make('Repeatable')
                ->help('Should the answers in this step be repeated based on a previous answer?')
                ->onlyOnForms()
                ->fillUsing(function (NovaRequest $request, FormModel $model, string $attribute, string $requestAttribute) {
                    unset($model[$requestAttribute]);
                })
                ->resolveUsing(function () {
                    return $this->repeatable_step_id !== null;
                }),

            Select::make('Repeatable Answer', 'repeatable_answer_id')
                ->help('Which numeric answer determines how many times this form is repeated?')
                ->hide()
                ->nullable()
                ->dependsOn('repeatable', function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->get('repeatable')) {
                        $field
                            ->show()
                            ->rules('required')
                            ->options(
                                AnswerModel::query()
                                    ->with('step.form')
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
                            );
                    }
                })
                ->hideFromIndex()
                ->hideFromDetail(function () {
                    return $this->repeatable_step_id === null;
                }),

            HasMany::make('Sections'),

            Select::make('TA Form Template')
                ->options(FormType::asArray())
                ->nullable(),

            SimpleRepeatable::make('Current Date Field Name', 'current_date_field', [
                Text::make('Field Name'),
            ])->dependsOn('ta_form_template', function (Select $field, NovaRequest $request, FormData $formData) {
                if ($formData->get('ta_form_template') === null) {
                    $field->hide();
                } else {
                    $field->show();
                }
            }),

            SimpleRepeatable::make('Signature Coordinates', 'signature_coords', [
                Text::make('Page Number')
                    ->dependsOn('group', function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->get('group') === FormGroup::Protocol) {
                            $field->show();
                        } else {
                            $field->hide();
                        }
                    }),
                Text::make('X')
                    ->dependsOn('group', function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->get('group') === FormGroup::Protocol) {
                            $field->show();
                        } else {
                            $field->hide();
                        }
                    }),
                Text::make('Y')
                    ->dependsOn('group', function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->get('group') === FormGroup::Protocol) {
                            $field->show();
                        } else {
                            $field->hide();
                        }
                    }),

            ])->dependsOn('ta_form_template', function (Select $field, NovaRequest $request, FormData $formData) {
                if ($formData->get('ta_form_template') === null) {
                    $field->hide();
                } else {
                    $field->show();
                }
            }),

            MorphMany::make('Conditions', 'conditions', Condition::class),
        ];
    }

    /**x
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
