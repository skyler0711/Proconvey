<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NoOfClients;
use App\Nova\Metrics\NoOfConveyancingFirms;
use App\Nova\Metrics\NoOfTeamMembers;
use Laravel\Nova\Dashboard;

class Main extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return 'Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            NoOfConveyancingFirms::make(),
            NoOfTeamMembers::make(),
            NoOfClients::make(),
        ];
    }
}
