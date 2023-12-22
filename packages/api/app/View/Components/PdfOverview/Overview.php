<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Overview extends Component
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
        $this->details = Arr::get($details, 'overview');
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
                return view('components.pdf-overview.overview', [
                    'address' => Arr::get($this->details, 'address'),
                    'tenure' => Arr::get($this->details, 'tenure'),
                ]);
            case PropertyType::Purchase:
                return view('components.pdf-overview.overview', [
                    'address' => Arr::get($this->details, 'address'),
                    'tenure' => Arr::get($this->details, 'tenure'),
                    'price' => Arr::get($this->details, 'price') ?? 'N/A',
                    'price_title' => 'Price',
                    'property_type' => Arr::get($this->details, 'property_type') ?? 'N/A',
                    'property_sub_type' => Arr::get($this->details, 'property_sub_type') ?? 'N/A',
                    'current_use' => Arr::get($this->details, 'current_use') ?? 'N/A',
                    'intended_use' => Arr::get($this->details, 'intended_use') ?? 'N/A',
                    'dependent_on_sale' => Arr::get($this->details, 'dependent_on_sale') ?? 'N/A',
                    'shared_ownership_percentage' => Arr::get($this->details, 'shared_ownership_percentage') ?? 'N/A',
                    'relationship_to_seller' => Arr::get($this->details, 'relationship_to_seller') ?? 'N/A',
                ]);
            case PropertyType::Remortgage:
                return view('components.pdf-overview.overview', [
                    'address' => Arr::get($this->details, 'address'),
                    'tenure' => Arr::get($this->details, 'tenure'),
                    'price' => Arr::get($this->details, 'price') ?? 'N/A',
                    'price_title' => 'Remortgage Amount',
                    'property_type' => Arr::get($this->details, 'property_type') ?? 'N/A',
                    'property_sub_type' => Arr::get($this->details, 'property_sub_type') ?? 'N/A',
                    'current_use' => Arr::get($this->details, 'current_use') ?? 'N/A',
                    'intended_use' => Arr::get($this->details, 'intended_use') ?? 'N/A',
                    'dependent_on_sale' => Arr::get($this->details, 'dependent_on_sale') ?? 'N/A',
                ]);
        }
    }
}
