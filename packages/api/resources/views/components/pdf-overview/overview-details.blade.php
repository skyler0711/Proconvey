@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))

@if ($property->type === 'Sale')
    <h2>Sale Details</h2>

    <div class="panel">
        <div>
            <div style="display: inline-block; margin-right: 10px">
                <strong>Sale type:</strong> {{ $sale_type }}
            </div>

            <div style="display: inline-block; margin-right: 10px">
                <strong>Sale status:</strong> {{ $sale_status }}
            </div>

            @if (isset($name))
                <div style="display: inline-block; margin-right: 10px">
                    <strong>Estate agent:</strong> {{ $name }}
                </div>
            @endif
        </div>

        <div>
            @if (isset($phone))
                <div style="display: inline-block; margin-right:10px">
                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                    {{ $phone }}
                </div>
            @endif

            @if (isset($address))
                <div style="display: inline-block; margin-right:10px">
                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                    {{ $address }}
                </div>
            @endif
        </div>
    </div>

    <div class="spacer"></div>

    <table>
        <tbody>
            @foreach ($owners as $ownerIndex => $owner)
                <tr>
                    <td class="table-header">
                        <span class="text-primary">Owner {{ $ownerIndex + 1 }}</span>
                    </td>
                </tr>

                <tr>
                    <td @style(['border-radius: 0' => !$loop->last])>
                        <div style="font-size: 18px">
                            <strong>{{ $owner['name'] }}</strong>
                        </div>
                        @if ($owner['type'] === 'Individual')
                            <div>
                                @if ($owner['email'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                            width="16" />
                                        {{ $owner['email'] }}
                                    </div>
                                @endif

                                @if ($owner['phone'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                            width="16" />
                                        {{ $owner['phone'] }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div>
                                @if ($owner['email'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                            width="16" />
                                        {{ $owner['email'] }}
                                    </div>
                                @endif

                                @if ($owner['phone'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                            width="16" />
                                        {{ $owner['phone'] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($property->type === 'Purchase')
    <table>
        <tbody>
            <tr class="table-header">
                <td>Estate Agent</td>
            </tr>

            <tr>
                <td>
                    <div>
                        @if (isset($estate_agent['name']))
                            <strong>{{ $estate_agent['name'] }}</strong>
                        @endif

                        @if (isset($estate_agent['name']))
                            <div>
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}"
                                    width="16" />
                                {{ $estate_agent['address'] }}
                            </div>
                        @endif

                        <div>
                            @if (isset($estate_agent['email']))
                                <div style="display: inline-block; margin-right:10px">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                        width="16" />
                                    {{ $estate_agent['email'] }}
                                </div>
                            @endif
                            @if (isset($estate_agent['phone']))
                                <div style="display: inline-block; margin-right:10px">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                        width="16" />
                                    {{ $estate_agent['phone'] }}
                                </div>
                            @endif
                        </div>

                        <div>
                            @if (isset($sale_type))
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Sale type:</strong> {{ $sale_type }}
                                </div>
                            @endif
                            @if (isset($sale_status))
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Auction:</strong> {{ $sale_status }}
                                </div>
                            @endif
                            @if (isset($deposit_paid))
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Deposit already paid:</strong> {{ $deposit_paid }}
                                    {{ $deposit_paid_amount }}
                                </div>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>
@endif

@if ($property->type !== 'Sale')
    <table>
        <tbody>
            <tr class="table-header">
                <td>Legal Representation</td>
            </tr>

            <tr>
                <td>
                    @if (isset($legal_representation['name']))
                        <div>
                            <strong>{{ $legal_representation['name'] }}</strong>

                            @if (isset($legal_representation['address']))
                                <div>
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}"
                                        width="16" />
                                    {{ $legal_representation['address'] }}
                                </div>
                            @endif

                            <div>
                                @if (isset($legal_representation['email']))
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                            width="16" />
                                        {{ $legal_representation['email'] }}
                                    </div>
                                @endif

                                @if (isset($legal_representation['phone']))
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                            width="16" />
                                        {{ $legal_representation['phone'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div>
                            Not Applicable
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
@endif
