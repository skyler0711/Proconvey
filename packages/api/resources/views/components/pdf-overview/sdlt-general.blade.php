@php($tickPlainIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-plain.png'))))
@php($tickSelectedIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-selected.png'))))

<div class="page-break"></div>

<table>
    <h2>SDLT Decleration</h2>

    <tbody>
        <tr class="table-header">
            <td>
                General
            </td>
        </tr>

        <tr>
            <td>
                <strong>Property</strong>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <p>Is the property moveable (e.g. mobile home, caravan or houseboat)?</p>

                    @isset($property_moveable)
                        <div>
                            <div style="display: inline-block; margin-right:20px">
                                <img style="margin-bottom: -4px; margin-right: 4px"
                                    src="{{ $property_moveable === 'Yes' ? $tickSelectedIcon : $tickPlainIcon }}"
                                    width="16" />
                                Yes
                            </div>
                            <div style="display: inline-block; margin-right:20px">
                                <img style="margin-bottom: -4px; margin-right: 4px"
                                    src="{{ $property_moveable === 'No' ? $tickSelectedIcon : $tickPlainIcon }}"
                                    width="16" />
                                No
                            </div>
                        </div>
                    @endisset
                </div>
            </td>
        </tr>
        <td>
            <div>
                <p>
                    Will the property be a mixture of residential and non-residential (like a flat with a shop
                    underneath it)?
                </p>

                @isset($mixture_of_residential_and_non_residential)
                    <div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $mixture_of_residential_and_non_residential === 'Yes' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            Yes
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $mixture_of_residential_and_non_residential === 'No' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            No
                        </div>
                    </div>
                @endisset
            </div>
        </td>
    </tbody>
</table>