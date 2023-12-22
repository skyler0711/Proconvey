<?php

namespace App\Nova;

use App\Enums\ConveyancerType;
use App\Nova\Filters\Location;
use App\Nova\Filters\NumberOfConveyancers;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Conveyancer extends Resource
{
    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Conveyancing Firms';
    }

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Conveyancer>
     */
    public static $model = \App\Models\Conveyancer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'company_number',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Type')
                ->options(ConveyancerType::asSelectArray())
                ->displayUsingLabels()
                ->rules('required'),

            Text::make('Name')
                ->rules(['required', 'max:254']),

            Text::make('Company Number')
                ->rules('max:254')
                ->hideFromIndex(),

            Text::make('SRA/CLC Number', 'sra_clc_number')
                ->rules('max:254')
                ->hideFromIndex(),

            Images::make('Logo', 'logo')
                ->rules(['required', 'max:5120'])
                ->uploadsToVapor(App::isProduction())
                ->hideFromIndex(),

            MorphOne::make('Address'),

            HasMany::make('Team Members', 'teamMembers', ConveyancerUser::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            NumberOfConveyancers::make(),
            Location::make(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
