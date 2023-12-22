<?php

namespace App\View\Components\PdfOverview\PurchaseFunds;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Loans extends Component
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
                if (Arr::get($this->details, 'loans') === null) {
                    return null;
                }

                return view('components.pdf-overview.purchase-funds.loans', [
                    'property' => ($this->property),
                    'lenders' => Arr::get($this->details, 'loans', '-'),
                ]);
                break;
        }
    }
}
