<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class OverviewDetails extends Component
{
    public Property $property;

    public array $details;

    public array $owners;

    public Collection $allSteps;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details, Collection $allSteps)
    {
        $this->property = $property;
        $this->details = Arr::get($details, 'overview_details');
        $this->owners = Arr::get($details, 'owners');
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
            case PropertyType::Sale:
                $estateAgent = $this->allSteps->firstWhere('type', StepType::EstateAgent)?->getCompiledAnswer($this->property);

                return view('components.pdf-overview.overview-details', [
                    'sale_type' => Arr::get($estateAgent, 'name')
                        ? 'Via estate agent'
                        : 'Not via estate agent',
                    'sale_status' => Arr::get($this->details, 'sale_status', '-'),
                    'name' => Arr::get($estateAgent, 'name'),
                    'phone' => Arr::get($estateAgent, 'phone'),
                    'email' => Arr::get($estateAgent, 'email'),
                    'address' => Arr::get($estateAgent, 'address_single_line'),
                    'owners' => $this->owners,
                ]);
            case PropertyType::Purchase:
                $estateAgent = $this->allSteps->firstWhere('type', StepType::EstateAgent)?->getCompiledAnswer($this->property);

                return view('components.pdf-overview.overview-details', [
                    'estate_agent' => [
                        'name' => Arr::get($estateAgent, 'name'),
                        'phone' => Arr::get($estateAgent, 'phone'),
                        'address' => Arr::get($estateAgent, 'address_single_line'),
                        'email' => Arr::get($estateAgent, 'email'),
                    ],
                    'sale_type' => Arr::get($this->details, 'sale_type')
                        ? 'Via estate agent'
                        : 'Not via estate agent',
                    'sale_status' => Arr::get($this->details, 'sale_status', '-'), // Used for displaying the auction header
                    'deposit_paid' => Arr::get($this->details, 'deposit_paid', 'N/A'),
                    'deposit_paid_amount' => Arr::get($this->details, 'deposit_paid_amount', ''),
                    'legal_representation' => [
                        'name' => Arr::get($this->details, 'legal_representation_name'),
                        'address' => Arr::get($this->details, 'legal_representation_address'),
                        'phone' => Arr::get($this->details, 'legal_representation_phone'),
                        'email' => Arr::get($this->details, 'legal_representation_email'),
                    ],
                ]);
            case PropertyType::Remortgage:
                return view('components.pdf-overview.overview-details', [
                    'legal_representation' => [
                        'name' => Arr::get($this->details, 'legal_representation_name'),
                        'address' => Arr::get($this->details, 'legal_representation_address'),
                        'phone' => Arr::get($this->details, 'legal_representation_phone'),
                        'email' => Arr::get($this->details, 'legal_representation_email'),
                    ],
                ]);
        }
    }
}
