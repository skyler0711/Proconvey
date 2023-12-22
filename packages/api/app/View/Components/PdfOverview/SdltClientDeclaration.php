<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SdltClientDeclaration extends Component
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
        $this->details = Arr::get($details, 'sdlt_client_declaration') ?? [];
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
                if (Arr::get($this->details, 'buyers', []) === []) {
                    return null;
                }

                return view('components.pdf-overview.sdlt-client-declaration', [
                    'property' => ($this->property),
                    'client_declaration' => Arr::get($this->details, 'client_declaration', '-'),
                    'buyers' => Arr::get($this->details, 'buyers', '-'),
                ]);
        }
    }
}
