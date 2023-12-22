<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class MortgagesAndCharges extends Component
{
    public Property $property;

    public array $mortgagesChargesLoans;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details)
    {
        $this->property = $property;
        $this->mortgagesChargesLoans = Arr::get($details, 'mortgages_charges_loans');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $parameters[] = [];

        switch ($this->property->type) {
            case PropertyType::Sale:
                $parameters = $this->mortgagesChargesLoans;
                break;
            case PropertyType::Remortgage:
                $parameters = $this->mortgagesChargesLoans;
                break;
            default:
                return null; // Do not load component
                break;
        }

        return view('components.pdf-overview.mortgages-and-charges', $parameters);
    }
}
