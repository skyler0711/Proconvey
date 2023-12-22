@php($tickPlainIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-plain.png'))))
@php($tickSelectedIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-selected.png'))))

<div class="page-break"></div>

<table>
    <tbody>
        <tr class="table-header">
            @isset($buyers)
                @foreach ($buyers as $buyerIndex => $buyer)
                    <td>
                        Client declaration {{ $buyerIndex + 1 }}
                    </td>
                @endforeach
            @endisset
        </tr>
    </tbody>

    <tbody>
        <tr>
            <td>
                <strong>
                    {{ $buyer['name'] }}
                </strong>
            </td>
        </tr>

        @foreach ($client_declaration as $client_declarationIndex => $declaration)
            @isset($declaration['first_time_buyer'])
                <tr>
                    <td>
                        <div>
                            <p>Whether bought, gifted or inherited, has the buyer ever owned any residential property or land anywhere in the world?</p>
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['first_time_buyer'] === 'Yes' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            Yes
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['first_time_buyer'] === 'No' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            No
                        </div>
                    </td>
                </tr>
            @endisset
            @isset($declaration['higher_or_lower'])
                <tr>
                    <td>
                        <div>
                            <p>After this purchase has completed will the buyer, and their spouses or civil partners, own
                                more than one property worth more than £40,000?</p>
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['higher_or_lower'] === 'Yes' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            Yes
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['higher_or_lower'] === 'No' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            No
                        </div>
                    </td>
                </tr>
            @endisset
            @isset($declaration['first_time_buyer_relief'])
                <tr>
                    <td>
                        <div>
                            <p>Will the property be the main residence for the buyer?</p>
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['first_time_buyer_relief'] === 'Yes' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            Yes
                        </div>
                        <div style="display: inline-block; margin-right:20px">
                            <img style="margin-bottom: -4px; margin-right: 4px"
                                src="{{ $declaration['first_time_buyer_relief'] === 'No' ? $tickSelectedIcon : $tickPlainIcon }}"
                                width="16" />
                            No
                        </div>
                    </td>
                </tr>
            @endisset
        @endforeach
</table>
