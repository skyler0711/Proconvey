<?php

namespace App\View\Components\PdfOverview\PurchaseFunds;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PurchaseFundsOther extends Component
{
    public Property $property;

    public array $details;

    public Collection $allSteps;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details, Collection $allSteps)
    {
        $this->property = $property;
        $this->details = Arr::get($details, 'purchase_funds') ?? [];
        $this->allSteps = $allSteps;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        switch ($this->property->type) {
            case PropertyType::Purchase:
            case PropertyType::Remortgage:
                if (Arr::get($this->details, 'other') === []) {
                    return null;
                }

                return view('components.pdf-overview.purchase-funds.purchase-funds-other', [
                    'property' => ($this->property),
                    'other' => Arr::get($this->details, 'other', '-'),
                ]);
                break;
        }
    }
}
