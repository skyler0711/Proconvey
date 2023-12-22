@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))

<div class="page-break"></div>

<table>

<h2>Related Transactions</h2>

    <tbody>

        @if (isset($transactions))
            @foreach ($transactions as $index => $transaction)
                <tr class="table-header">
                    <td>
                        Related Transaction {{ $index + 1 }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div>
                                <b>Owners:</b> {{ implode(', ', $transaction['names']) }}
                            </div>
                            <div style="display: inline-block; margin-top: 8px">
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                                <strong>Correspondence address:</strong> {{ implode(', ', $transaction['address']) }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif

        @if (isset($purchaseRepresentation))
            <tr class="table-header">
                <td>
                    Related Transaction 1
                </td>
            </tr>
            <tr>
                <td>
                    <div>

                        <div style="display: inline-block">
                            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                            <strong>{{ $dependentAddress }}</strong>
                        </div>

                        <div style="margin-top: 24px">

                            <div>
                                <strong>Legal Representation:</strong> {{ $purchaseRepresentation['name'] ?? 'N/a'  }}
                            </div>

                            @if (isset($purchaseRepresentation['address']))
                                <div style="margin-top: 8px">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                                    {{ $purchaseRepresentation['address'] }}
                                </div>
                            @endif

                            <div style="display: inline-block; margin-top: 8px">
                                @if (isset($purchaseRepresentation['email']))
                                    <div style="display: inline-block; margin-right: 10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                                        {{ $purchaseRepresentation['email'] }}
                                    </div>
                                @endif

                                @if (isset($purchaseRepresentation['phone']))
                                    <div style="display: inline-block">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                                        {{ $purchaseRepresentation['phone'] }}
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </td>
            </tr>
        @endif

    </tbody>

</table>