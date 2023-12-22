<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class CurrentOwnership extends Component
{
    private Property $property;

    private $currentOwnership;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details)
    {
        $this->property = $property;
        $this->currentOwnership = Arr::get($details, 'current_ownership');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $parameters[] = [];

        switch ($this->property->type) {
            case PropertyType::Remortgage:
                $parameters['owners'] = $this->currentOwnership['owners'];
                break;
            default:
                return null; // Do not load component
                break;
        }

        return view('components.pdf-overview.current-ownership', $parameters);
    }
}
