<?php

namespace App\View\Components\PdfOverview;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class RelatedTransactions extends Component
{
    public Property $property;

    public array $relatedTransactions;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $details)
    {
        $this->property = $property;
        $this->relatedTransactions = Arr::get($details, 'related_transactions', []);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $parameters = [];

        switch ($this->property->type) {
            case PropertyType::Sale:
                if (count(Arr::get($this->relatedTransactions, 'transactions', [])) === 0) {
                    return null;
                }

                $parameters['transactions'] = $this->relatedTransactions['transactions'];
                break;
            case PropertyType::Remortgage:
            case PropertyType::Purchase:
                if (! isset($this->relatedTransactions['dependant_address'])) {
                    return null;
                }

                $parameters['purchaseRepresentation'] = $this->relatedTransactions['purchase_representation'];
                $parameters['dependentAddress'] = $this->relatedTransactions['dependant_address'];
                break;
        }

        return view('components.pdf-overview.related-transactions', $parameters);
    }
}
