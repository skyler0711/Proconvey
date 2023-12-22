@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))

<tr class="table-header">
    <td>
        Representative {{ $index + 1 }}
    </td>
</tr>

<tr>
    <td>
        <div style="font-size: 18px">
            <strong>{{ $name }}</strong>
        </div>

        <div>
            <div style="display: inline-block; margin-right:10px">
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                {{ $email }}
            </div>

            <div style="display: inline-block; margin-right:10px">
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                {{ $phone }}
            </div>

            <div>
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                <strong>Correspondence address:</strong>
                {{ $address }}
            </div>
        </div>

        <div>
            <div style="display: inline-block; margin-right:10px">
                <strong>Name change:</strong> {{ $name_change }}
            </div>

            @if ($name_change === 'Yes')
                <div style="display: inline-block; margin-right:10px">
                    <strong>Reason for name change:</strong> {{ $name_change_reason }}
                </div>
                <div style="display: inline-block; margin-right:10px">
                    <strong>Proof of name change:</strong> {{ $name_change_proof }}
                </div>
            @endif
        </div>
    </td>
</tr>
