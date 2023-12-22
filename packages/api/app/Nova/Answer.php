<?php

namespace App\Nova;

use App\Enums\AnswerType;
use App\Models\Answer as AnswerModel;
use BenSampo\Enum\Rules\EnumValue;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;

class Answer extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Answer>
     */
    public static $model = \App\Models\Answer::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * The default ordering for the resource.
     */
    public static $orderBy = [
        'created_at' => 'asc',
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        if (optional($this->getDetails())->label) {
            return "{$this->getDetails()->label} ({$this->type})";
        }

        return $this->type;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('Step'),

            Select::make('Type')
                ->options(AnswerType::asSelectArray())
                ->rules('required', new EnumValue(AnswerType::class))
                ->displayUsingLabels(),

            Text::make('Details', 'unused_details')
                ->onlyOnIndex()
                ->displayUsing(function () {
                    switch ($this->type) {
                        case AnswerType::SingleSelect:
                        case AnswerType::Dropdown:
                            return collect($this->getDetails()?->options ?? [])->pluck('value')->join(', ');
                        default:
                            return '—';
                    }
                })
                ->fillUsing(function (NovaRequest $request, \App\Models\Step $model, string $attribute, string $requestAttribute) {
                    unset($model[$requestAttribute]);
                }),

            Text::make('Label')
                ->hide()
                ->hideFromDetail(function () {
                    return is_null($this->getDetails()) || ! property_exists($this->getDetails(), 'label');
                })
                ->dependsOn(['type'], function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === AnswerType::Text || $formData->type === AnswerType::Dropdown) {
                        $field->show()->rules('required', 'max:254');
                    }
                })
                ->fillUsing(function (NovaRequest $request, AnswerModel $answer, string $attribute, string $requestAttribute) {
                    $label = $request->input($requestAttribute);
                    if ($label) {
                        $answer->setDetails([
                            ...(array) $answer->getDetails() ?? [],
                            'label' => $label,
                        ]);
                    }
                })
                ->resolveUsing(function () {
                    return is_null($this->getDetails()) || ! property_exists($this->getDetails(), 'label')
                        ? null
                        : $this->getDetails()->label;
                }),

            Text::make('Placeholder')
                ->hide()
                ->hideFromDetail(function () {
                    return is_null($this->getDetails()) || ! property_exists($this->getDetails(), 'placeholder');
                })
                ->dependsOn(['type'], function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === AnswerType::Text) {
                        $field->show()->rules('max:254');
                    }
                })
                ->fillUsing(function (NovaRequest $request, AnswerModel $answer, string $attribute, string $requestAttribute) {
                    $placeholder = $request->input($requestAttribute);
                    if ($placeholder) {
                        $answer->setDetails([
                            ...(array) $answer->getDetails() ?? [],
                            'placeholder' => $placeholder,
                        ]);
                    }
                })
                ->resolveUsing(function () {
                    return is_null($this->getDetails()) || ! property_exists($this->getDetails(), 'placeholder')
                        ? null
                        : $this->getDetails()->placeholder;
                })
                ->hideFromIndex(),

            SimpleRepeatable::make('Options', 'details', [
                Text::make('Value')
                    ->rules('required', 'max:254'),
            ])
                ->hide()
                ->hideFromDetail(function () {
                    return is_null($this->getDetails()) || ! property_exists($this->getDetails(), 'options');
                })
                ->dependsOn(['type'], function (SimpleRepeatable $field, NovaRequest $request, FormData $formData) {
                    if ($formData->type === AnswerType::SingleSelect || $formData->type === AnswerType::Dropdown) {
                        $field->show()->rules('required', 'min:1');
                    }
                })
                ->fillUsing(function (NovaRequest $request, AnswerModel $answer, string $attribute, string $requestAttribute) {
                    $options = json_decode($request->input($requestAttribute));
                    if (count($options) > 0) {
                        $answer->setDetails([
                            ...(array) $answer->getDetails() ?? [],
                            'options' => $options,
                        ]);
                    }
                })
                ->resolveUsing(function (?object $details) {
                    return is_null($details) || ! property_exists($details, 'options')
                        ? null
                        : $details->options;
                }),

            HasMany::make('Validation Rules', 'validationRules', ValidationRule::class),

            Text::make('PDF Form Field Name')
                ->fillUsing(function (NovaRequest $request, AnswerModel $answer, string $attribute, string $requestAttribute) {
                    $pdfFormFieldName = $request->input($requestAttribute);
                    if ($pdfFormFieldName) {
                        $answer->details = [
                            ...(array) $answer->details ?? [],
                            'pdfFormFieldName' => $pdfFormFieldName,
                        ];
                    }
                })
                ->resolveUsing(function () {
                    return is_null($this->details) || ! property_exists($this->details, 'pdfFormFieldName')
                        ? null
                        : $this->details->pdfFormFieldName;
                }),

            MorphMany::make('Conditions', 'conditions', Condition::class),
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
