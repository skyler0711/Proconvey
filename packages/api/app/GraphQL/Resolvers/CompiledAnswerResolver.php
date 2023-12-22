<?php

namespace App\GraphQL\Resolvers;

use App\Models\Property;
use App\Models\Step;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CompiledAnswerResolver
{
    public function __invoke(Step $step, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($resolveInfo->path[0] !== 'property') {
            return null;
        }

        if (! array_key_exists('id', $resolveInfo->variableValues)) {
            return null;
        }

        return $step->getCompiledAnswer(
            Property::find($resolveInfo->variableValues['id']),
        );
    }
}
