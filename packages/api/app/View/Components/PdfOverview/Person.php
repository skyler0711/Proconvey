<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Person extends Component
{
    public Property $property;

    public array $person;

    public int $personIndex;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $person, int $personIndex)
    {
        $this->property = $property;
        $this->person = $person;
        $this->personIndex = $personIndex;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $attributes = collect([
            'property' => $this->property,
            'person_index' => $this->personIndex,
            'person' => $this->person,
            'type' => Arr::get($this->person, 'type'),
        ]);

        switch ($this->property->type) {
            case PropertyType::Sale:
                $attributes->put('person_type', 'Owner');
                break;
            case PropertyType::Purchase:
                $attributes->put('person_type', 'Buyer');
                break;
            case PropertyType::Remortgage:
                $attributes->put('person_type', 'Buyer');
                break;
        }

        return view('components.pdf-overview.person', $attributes->toArray());
    }
}
