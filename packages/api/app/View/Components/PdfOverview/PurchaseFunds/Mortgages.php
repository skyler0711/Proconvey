<?php

namespace App\View\Components\PdfOverview\PurchaseFunds;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Mortgages extends Component
{
    public Property $property;

    public array $details;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details)
    {
        $this->property = $property;
        $this->details = Arr::get($details, 'purchase_funds') ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        switch ($this->property->type) {
            case PropertyType::Remortgage:
            case PropertyType::Purchase:
                if (Arr::get($this->details, 'mortgage_lender') === null) {
                    return null;
                }

                return view('components.pdf-overview.purchase-funds.mortgages', [
                    'property' => ($this->property),
                    'mortgage_lender' => Arr::get($this->details, 'mortgage_lender', '-'),
                    'mortgage_amount' => Arr::get($this->details, 'mortgage_amount', '-'),
                ]);
                break;
        }
    }
}
