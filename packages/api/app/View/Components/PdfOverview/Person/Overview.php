<?php

namespace App\View\Components\PdfOverview\Person;

use App\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Overview extends Component
{
    public Property $property;

    public array $overview;

    public array $contactDetails;

    public ?string $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Property $property, array $person)
    {
        $this->property = $property;
        $this->overview = Arr::get($person, 'overview');
        $this->contactDetails = Arr::get($person, 'contact_details');
        $this->type = Arr::get($person, 'type');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $attributes = collect();

        $attributes->put('type', $this->type);

        // Overview
        $attributes->put('name', Arr::get($this->overview, 'name'));
        $attributes->put('company_number', Arr::get($this->overview, 'company_number'));

        // Line 1
        $attributes->put('name_change', Arr::get($this->overview, 'name_change'));
        $attributes->put('name_change_reason', Arr::get($this->overview, 'name_change_reason'));
        $attributes->put('name_change_proof', Arr::get($this->overview, 'name_change_proof'));

        // Line 2 - Company
        $attributes->put('vat_status', Arr::get($this->overview, 'vat_status') ?? 'N/A');
        $attributes->put('vat_number', Arr::get($this->overview, 'vat_number') ?? 'N/A');

        // Line 2 - Person
        $attributes->put('date_of_birth', Arr::get($this->overview, 'date_of_birth'));
        $attributes->put('occupation', Arr::get($this->overview, 'occupation'));
        $attributes->put('national_insurance', Arr::get($this->overview, 'national_insurance'));

        // Line 3
        $attributes->put('status', Arr::get($this->overview, 'status'));

        // Contact Details
        $attributes->put('address', Arr::get($this->contactDetails, 'address'));
        $attributes->put('post_completion_address', Arr::get($this->contactDetails, 'post_address'));
        $attributes->put('email', Arr::get($this->contactDetails, 'email'));
        $attributes->put('phone', Arr::get($this->contactDetails, 'phone'));
        $attributes->put('phone_alt', Arr::get($this->contactDetails, 'phone_alt'));

        return view('components.pdf-overview.person.overview', $attributes->toArray());
    }
}
