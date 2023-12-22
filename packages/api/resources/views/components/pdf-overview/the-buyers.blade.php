@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($percentageIcon = file_get_contents(resource_path('img/pdf-icons/percentage.svg')))

<table>
    <h2 style="margin-top: 8px">
        The Buyers{{ $property->type === 'Remortgage' ? ' (Remortgaging)' : '' }}
    </h2>

    <tbody>
        @if ($property->type !== 'Sale')
            <tr class="table-header">
                <td>
                    Legal capacity
                </td>
            </tr>
        @endif
        <tr>
            <td>
                @if ($property->type === 'Sale')
                    <div>
                        <strong>Conveyancer firm:</strong> {{ $property->conveyancer->name }}
                    </div>

                    <div>
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                        {{ $property->conveyancer->address->single_line }}
                    </div>
                @else
                    <div>
                        <div style="display: inline-block; margin-right:30px">
                            <strong>{{ $buyer_capacity }}</strong>
                        </div>
                        <div style="display: inline-block; margin-right:10px">
                            <strong>Trust deed:</strong> {{ $trust_deed }}
                        </div>
                    </div>

                    <div>
                        {{ $trust_deed_details }}
                    </div>
                @endif
            </td>
        </tr>

        @if ($property->type !== 'Sale')
    </tbody>
</table>

<div class="spacer"></div>

<table>
    <tbody>
        @endif

        @foreach ($buyers as $buyerIndex => $buyer)
            <tr class="table-header">
                <td>
                    Buyer {{ $buyerIndex + 1 }}
                </td>
            </tr>

            <tr>
                <td>
                    <div>
                        <strong>{{ $buyer['name'] }}</strong>
                    </div>

                    <div>
                        @if ($buyer['email'])
                            <div style="display: inline-block; margin-right:10px">
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                    width="16" />
                                {{ $buyer['email'] }}
                            </div>
                        @endif

                        @if ($buyer['phone'])
                            <div style="display: inline-block; margin-right:10px">
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                    width="16" />
                                {{ $buyer['phone'] }}
                            </div>
                        @endif

                        @if (isset($buyer['share']))
                            <div style="text-align: right">
                                {!! $percentageIcon !!} <strong>Shares:</strong> {{ $buyer['share'] }}
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
