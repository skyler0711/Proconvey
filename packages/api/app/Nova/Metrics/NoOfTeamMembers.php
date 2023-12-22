<?php

namespace App\Nova\Metrics;

use App\Enums\UserRole;
use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class NoOfTeamMembers extends Value
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return 'No. of Team Members';
    }

    /**
     * The element's icon.
     *
     * @var string
     */
    public $icon = 'user';

    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $query = User::query()
            ->where('role', UserRole::Conveyancer);

        if ($request->range === 'ALL') {
            return $this->result(
                $query->count()
            );
        }

        $range = $request->range ?? 1;

        $currentRange = $this->currentRange($range, config('app.timezone'));
        $previousRange = $this->previousRange($range, config('app.timezone'));

        $previousValue = (clone $query)
            ->whereBetween('created_at', $this->formatQueryDateBetween($previousRange))
            ->count();

        return $this->result(
            (clone $query)
                ->whereBetween('created_at', $this->formatQueryDateBetween($currentRange))
                ->count()
        )->previous($previousValue);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }
}
