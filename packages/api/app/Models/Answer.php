<?php

namespace App\Models;

use App\DTO\AnswerDetails\AnswerDetailsAddress;
use App\DTO\AnswerDetails\AnswerDetailsCheckbox;
use App\DTO\AnswerDetails\AnswerDetailsDataTable;
use App\DTO\AnswerDetails\AnswerDetailsDropdown;
use App\DTO\AnswerDetails\AnswerDetailsFile;
use App\DTO\AnswerDetails\AnswerDetailsOwnerDropdown;
use App\DTO\AnswerDetails\AnswerDetailsSingleSelect;
use App\DTO\AnswerDetails\AnswerDetailsText;
use App\DTO\AnswerDetails\AnswerDetailsTextarea;
use App\Enums\AnswerType;
use App\Rules\FileAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Answer extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'type',
        'details',
    ];

    public function getDetails(?Property $property = null)
    {
        $value = $this->attributes['details'];

        if (is_null($value)) {
            return null;
        }

        switch ($this->type) {
            case AnswerType::Address:
                return AnswerDetailsAddress::fromJson($value);
            case AnswerType::DataTable:
                return AnswerDetailsDataTable::fromJson($value);
            case AnswerType::SingleSelect:
                return AnswerDetailsSingleSelect::fromJson($value);
            case AnswerType::MultiSelect:
                return AnswerDetailsSingleSelect::fromJson($value);
            case AnswerType::Text:
                return AnswerDetailsText::fromJson($value);
            case AnswerType::Textarea:
                return AnswerDetailsTextarea::fromJson($value);
            case AnswerType::Dropdown:
                return AnswerDetailsDropdown::fromJson($value);
            case AnswerType::Checkbox:
                return AnswerDetailsCheckbox::fromJson($value);
            case AnswerType::OwnerDropdown:
            case AnswerType::PersonMultiSelect:
                $details = AnswerDetailsOwnerDropdown::fromJson($value);
                if ($property) {
                    $compiledAnswer = optional($this->step)->getCompiledAnswer($property);
                }

                if (isset($compiledAnswer) && $compiledAnswer) {
                    $details->options = $compiledAnswer;
                }

                return $details;
            case AnswerType::File:
                return AnswerDetailsFile::fromJson($value);
        }

        throw new \Exception("Unknown answer type: \"$this->type\"");
    }

    public function getDetailsAttribute()
    {
        return $this->getDetails();
    }

    public function setDetails($value)
    {
        if (is_null($value)) {
            $this->attributes['details'] = null;

            return;
        }

        if (is_string($value)) {
            $this->attributes['details'] = $value;

            return;
        }

        $this->attributes['details'] = json_encode($value);
    }

    public function setDetailsAttribute($value)
    {
        return $this->setDetails($value);
    }

    /**
     * Step relationship
     */
    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * Validation rules relationship
     */
    public function validationRules()
    {
        return $this->hasMany(ValidationRule::class);
    }

    /**
     * Conditions relationship
     */
    public function conditions()
    {
        return $this->morphMany(Condition::class, 'conditionable');
    }

    /**
     * Condition triggers relationship
     * This is a list of conditionals that depend on this answer
     */
    public function conditionTriggers()
    {
        return $this->hasMany(Condition::class);
    }

    /**
     * Provided answers relationship
     */
    public function providedAnswers(): HasMany
    {
        return $this->hasMany(ProvidedAnswer::class);
    }

    /**
     * Get the validation rules for this answer
     */
    public function getTypeValidationRules(Property $property): array
    {
        switch ($this->type) {
            case AnswerType::Address:
                return [
                    'array',
                    [
                        'line_1' => ['required'],
                        'line_2' => ['nullable'],
                        'city' => ['required'],
                        'postcode' => ['required'],
                    ],
                ];
            case AnswerType::SingleSelect:
                return [
                    Rule::in(collect($this->getDetails($property)->options)->pluck('value')),
                ];
            case AnswerType::MultiSelect:
                return [
                    'array',
                    [
                        '*' => Rule::in(collect($this->getDetails($property)->options)->pluck('value')),
                    ],
                ];
            case AnswerType::Number:
                return [
                    'numeric',
                ];
            case AnswerType::Dropdown:
                return [
                    Rule::in(collect($this->getDetails($property)->options)->pluck('value')),
                ];
            case AnswerType::Text:
                return [];
            case AnswerType::Textarea:
                return [];
            case AnswerType::Checkbox:
                return [
                    'boolean',
                ];
            case AnswerType::OwnerDropdown:
                return [
                    Rule::in(collect($this->step->getCompiledAnswer($property))->pluck('value')),
                ];
            case AnswerType::DataTable:
                $validators = collect([
                    'rows' => ['required', 'array'],
                    'columns' => ['required', 'array'],
                ]);

                $numberOfRows = count($this->getDetails($property)->rows);

                if ($numberOfRows > 0) {
                    foreach (range(0, $numberOfRows - 1) as $index) {
                        $validators = $validators->merge([
                            "columns.$index" => ['required'],
                        ]);
                    }
                }

                return [$validators->toArray()];
            case AnswerType::File:
                return [
                    new FileAnswer($property),
                ];
        }

        return [];
    }

    /**
     * Repeatable step relationship
     */
    public function repeatableStep()
    {
        return $this->hasMany(Step::class, 'repeatable_answer_id', 'id');
    }

    /**
     * Form relationship
     */
    public function form()
    {
        return $this->hasOneDeep(
            Form::class,
            [Step::class, Section::class],
            ['id', 'id', 'id'],
            ['step_id', 'section_id', 'form_id'],
        );
    }
}
