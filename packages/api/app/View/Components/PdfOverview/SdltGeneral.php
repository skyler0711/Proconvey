<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SdltGeneral extends Component
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
        $this->details = Arr::get($details, 'sdlt_general') ?? [];
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

                return view('components.pdf-overview.sdlt-general', [
                    'property' => $this->property,
                    'property_moveable' => Arr::get($this->details, 'property_moveable', '-'),
                    'mixture_of_residential_and_non_residential' => Arr::get($this->details, 'mixture_of_residential_and_non_residential', '-'),
                ]);
        }
    }
}
