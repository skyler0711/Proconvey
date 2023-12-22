<?php

namespace App\GraphQL\Resolvers;

use App\Models\Answer;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AnswerProvidedAnswerResolver
{
    public function __invoke(Answer $answer, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $providedAnswer = $answer->providedAnswers()
            ->where('property_id', $args['property_id'])
            ->where('active_form_id', $args['active_form_id'])
            ->get();

        return $providedAnswer;
    }
}
