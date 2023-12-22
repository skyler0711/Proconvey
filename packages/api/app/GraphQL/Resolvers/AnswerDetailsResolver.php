<?php

namespace App\GraphQL\Resolvers;

use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Property;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AnswerDetailsResolver
{
    public function __invoke(Answer $answer, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($resolveInfo->path[0] !== 'property') {
            return $answer->details;
        }

        if (! array_key_exists('id', $resolveInfo->variableValues)) {
            return $answer->details;
        }

        // TODO: This currently causes loads of DB queries as part of the property progress query
        return in_array($answer->step->type, StepType::getGenerationSteps())
            ? $answer->getDetails(Property::find($resolveInfo->variableValues['id']))
            : $answer->details;
    }
}
