<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class NumberOfConveyancers extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        return match ($value) {
            '1' => $query->has('teamMembers', '=', 1),
            '2-5' => $query->has('teamMembers', '>=', 2)->has('teamMembers', '<=', 5),
            '6-10' => $query->has('teamMembers', '>=', 6)->has('teamMembers', '<=', 10),
            '11-20' => $query->has('teamMembers', '>=', 11)->has('teamMembers', '<=', 20),
            '21-30' => $query->has('teamMembers', '>=', 21)->has('teamMembers', '<=', 30),
            '31-50' => $query->has('teamMembers', '>=', 31)->has('teamMembers', '<=', 50),
            '51' => $query->has('teamMembers', '>=', 51),
        };
    }

    /**
     * Get the filter's available options.
     *
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            '1 Conveyancer' => 1,
            '2-5 Conveyancers' => '2-5',
            '6-10 Conveyancers' => '6-10',
            '11-20 Conveyancers' => '11-20',
            '21-30 Conveyancers' => '21-30',
            '31-50 Conveyancers' => '31-50',
            '50+ Conveyancers' => '51',
        ];
    }
}
