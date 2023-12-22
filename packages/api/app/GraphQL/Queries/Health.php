<?php

namespace App\GraphQL\Queries;

class Health
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return true;
    }
}
