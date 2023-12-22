<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class TheBuyers extends Component
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
        $this->details = Arr::get($details, 'buyers');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $args = [
            'property' => $this->property,
            'buyers' => Arr::get($this->details, 'buyers') ?? [],
        ];

        switch ($this->property->type) {
            case PropertyType::Sale:
                return view('components.pdf-overview.the-buyers', $args);
            case PropertyType::Purchase:
            case PropertyType::Remortgage:
                return view('components.pdf-overview.the-buyers', [
                    'buyer_capacity' => Arr::get($this->details, 'buyer_capacity') ?? 'N/A',
                    'trust_deed' => Arr::get($this->details, 'trust_deed') ?? 'N/A',
                    'trust_deed_details' => Arr::get($this->details, 'trust_deed_details'),
                    ...$args,
                ]);
        }
    }
}
