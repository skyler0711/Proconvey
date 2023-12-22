@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))

<div class="page-break"></div>

<h2>
    Current Ownership
</h2>

<table>

    <tbody>

        @foreach ($owners as $index => $owner)

            <tr class="table-header">

                <td>
                    Owner {{ $index + 1 }}
                </td>

            </tr>

            <tr>

                <td>

                    <div>
                        <strong>{{ $owner['full_name'] }}</strong>
                    </div>

                    <div style="display: inline-block; margin-top: 8px">

                        <div style="display: inline-block; margin-right: 10px">
                            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                            {{ $owner['email'] }}
                        </div>

                        <div style="display: inline-block">
                            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                            +{{ $owner['phone'] }}
                        </div>

                    </div>

                </td>

            </tr>

        @endforeach

    </tbody>

</table>