<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class TheSellers extends Component
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
        $this->details = Arr::get($details, 'sellers') ?? [];
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
                return view('components.pdf-overview.the-sellers', [
                    'property' => ($this->property),
                    'sellers' => Arr::get($this->details, 'sellers', '-'),
                    'solicitor' => Arr::get($this->details, 'solicitor', '-'),
                ]);
        }
    }
}
