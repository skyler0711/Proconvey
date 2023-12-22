<?php

namespace App\GraphQL\Mutations\Client;

use App\Models\Answer;
use App\Models\ProvidedAnswer;
use App\Models\Step;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Throwable;

final class DeleteMortgage
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $step = Step::find($args['step_id']);
        $removalIndex = $args['charge_index'];

        $answerIds = $step->answers->pluck('id');

        $providedAnswers = ProvidedAnswer::where('property_id', $args['property_id'])
            ->whereIn('answer_id', $answerIds)
            ->get();

        $providedRepeatAmount = $step->repeatableAnswer
            ->providedAnswers()
            ->where('property_id', $args['property_id'])
            ->first();

        // This question should always exist
        $repaymentChargesAvailableStep = Step::firstWhere('question', 'Are there any mortgages or charges secured against the property?');
        $repaymentChargesAvailableAnswer = Answer::firstWhere('step_id', $repaymentChargesAvailableStep->id)
            ->providedAnswers()
            ->firstWhere('property_id', $args['property_id']);

        $shouldDecreaseRepeatCount = false;

        try {
            DB::transaction(function () use ($providedAnswers, $removalIndex, &$providedRepeatAmount, &$shouldDecreaseRepeatCount, &$repaymentChargesAvailableAnswer) {
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
                        if ($newValue === 0) {
                            $repaymentChargesAvailableAnswer->value = 'No';
                            $repaymentChargesAvailableAnswer->save();
                        }

                        $providedRepeatAmount->value = $newValue;
                        $providedRepeatAmount->save();
                    }
                }
            });
        } catch (Throwable $th) {
            throw Exception($th);
        }

        return true;
    }
}
