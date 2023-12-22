<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ClientBankDetails extends Component
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
        $this->details = Arr::get($details, 'client_bank_details') ?? [];
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
            case PropertyType::Remortgage:
            case PropertyType::Purchase:
                return view('components.pdf-overview.client-bank-details', [
                    'property' => ($this->property),
                    'account_name' => Arr::get($this->details, 'account_name', '-'),
                    'account_number' => Arr::get($this->details, 'account_number', '-'),
                    'sort_code' => Arr::get($this->details, 'sort_code', '-'),
                ]);
        }
    }
}
