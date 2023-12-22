@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))

<table>
    <h2>Giftors</h2>

    <tbody>

        @foreach ($giftors as $giftorIndex => $giftor)
            <tr class="table-header">
                <td>
                    Giftor {{ $giftorIndex + 1 }}
                </td>
                <td>
                    Amount
                </td>
            </tr>

            <tr>
                <td>
                    <div>
                        {{ $giftor['name'] }}
                    </div>
                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                    {{ $giftor['address'] }}
                    <div>
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                        {{ $giftor['email'] }}
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                        {{ $giftor['phone'] }}
                    </div>
                </td>
                <td>
                    <div>
                        £{{ $giftor['amount_being_loaned'] }}
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
