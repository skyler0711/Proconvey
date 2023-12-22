<?php

namespace App\View\Components\PdfOverview\Person\Representation;

use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Representative extends Component
{
    public int $index;

    public array $person;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($index, array $representationRepresentative)
    {
        $this->index = $index;
        $this->person = $representationRepresentative;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.pdf-overview.person.representation.representative', [
            'index' => $this->index,
            'name' => Arr::get($this->person, 'name'),

            'name_change' => Arr::get($this->person, 'name_change') === 'N/A'
                ? 'No'
                : Arr::get($this->person, 'name_change'),
            'name_change_reason' => Arr::get($this->person, 'name_change_reason') ?? 'N/A',
            'name_change_proof' => Arr::get($this->person, 'name_change_proof') ?? 'N/A',

            'email' => Arr::get($this->person, 'email'),
            'phone' => Arr::get($this->person, 'phone'),
            'address' => Arr::get($this->person, 'address'),
        ]);
    }
}
