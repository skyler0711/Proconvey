<?php

namespace App\GraphQL\Mutations\Conveyancer;

use App\Models\Property;
use App\Models\ProvidedAnswer;
use App\Models\Step;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

final class RemoveGiftor
{
    public function __invoke($_, array $args)
    {
        $step = Step::find($args['input']['step_id']);
        $removalIndex = $args['input']['giftor_index'];

        $property = Property::find(Arr::get($args['input'], 'property_id'));

        $answerIds = $step->answers->pluck('id');

        $providedAnswers = ProvidedAnswer::where('property_id', $property->id)
            ->whereIn('answer_id', $answerIds)
            ->get();

        $providedRepeatAmount = $step->repeatableAnswer
            ->providedAnswers()
            ->where('property_id', $property->id)
            ->first();

        $shouldDecreaseRepeatCount = false;

        try {
            DB::transaction(function () use ($providedAnswers, $removalIndex, &$providedRepeatAmount, &$shouldDecreaseRepeatCount) {
                $providedAnswers->each(function (ProvidedAnswer $providedAnswer) use ($removalIndex, &$shouldDecreaseRepeatCount) {
                    if (isset($providedAnswer->value[$removalIndex])) {
                        $shouldDecreaseRepeatCount = true;

                        $answers = $providedAnswer->value;

                        if (count($answers) <= 1) {
                            $providedAnswer->delete();
                        } else {
                            array_splice($answers, intval($removalIndex), 1);
                            $providedAnswer->value = $answers;
                            $providedAnswer->save();
                        }
                    }
                });

                if ($shouldDecreaseRepeatCount) {
                    $newValue = strval(intval($providedRepeatAmount->value) - 1);

                    if ($newValue >= 0) {
                        $providedRepeatAmount->value = $newValue;
                        $providedRepeatAmount->save();
                    }
                }
            });
        } catch (Throwable $th) {
            throw Exception($th);
        }

        // Detach Giftor from Property
        $giftor = User::where('id', Arr::get($args['input'], 'giftor_id'))->firstOrFail();
        $property->users()->detach($giftor->id);

        return true;
    }
}
