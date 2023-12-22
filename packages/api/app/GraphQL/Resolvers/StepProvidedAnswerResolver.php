<?php

namespace App\GraphQL\Resolvers;

use App\Models\ProvidedAnswer;
use App\Models\Step;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class StepProvidedAnswerResolver
{
    public function __invoke(Step $step, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $answerIds = $step->answers->pluck('id');

        $providedAnswers = ProvidedAnswer::query()
            ->where('property_id', $args['property_id'])
            ->whereIn('answer_id', $answerIds)
            ->get();

        return $providedAnswers;
    }
}
