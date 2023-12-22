<?php

namespace App\View\Components\PdfOverview\Person;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Representation extends Component
{
    public Property $property;

    public array $representationAttributes;

    public ?string $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $person)
    {
        $this->property = $property;
        $this->representationAttributes = Arr::get($person, 'representation');
        $this->type = Arr::get($person, 'type');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $attributes = collect([
            'title_prefix' => $this->type === 'Company'
                ? 'Company'
                : match ($this->property->type) {
                    PropertyType::Sale => 'Owner',
                    PropertyType::Purchase, PropertyType::Remortgage => 'Buyer',
                },
            'type' => $this->type,
            'representation' => Arr::get($this->representationAttributes, 'representation'),
            'authority' => Arr::get($this->representationAttributes, 'authority'),
            'application_status' => Arr::get($this->representationAttributes, 'application_status'),
            'representatives' => Arr::get($this->representationAttributes, 'representatives', []),
        ]);

        return view('components.pdf-overview.person.representation', $attributes->toArray());
    }
}
