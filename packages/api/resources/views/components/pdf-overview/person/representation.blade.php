@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))

<table>
    <h2>{{ $title_prefix }} Representation</h2>

    <tbody>
        <tr>
            <td>
                @if ($representation)
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Representation:</strong> {{ $representation }}
                    </div>
                @endif

                @if ($application_status)
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Application status:</strong> {{ $application_status }}
                    </div>
                @endif

                @if ($authority)
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Authority:</strong> {{ $authority }}
                    </div>
                @endif
            </td>
        </tr>

        @if (count($representatives ?? []) > 0)
            @foreach ($representatives as $representativeindex => $representative)
                <tr class="table-header">
                    <td>
                        {{ $type === 'Company' ? 'Company representative' : 'Representative' }}
                        {{ $representativeindex + 1 }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <div style="font-size: 12px">
                            <strong>{{ $representative['name'] }}</strong>
                        </div>

                        <div>
                            <div style="display: inline-block; margin-right:10px">
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                    width="16" />
                                {{ $representative['email'] }}
                            </div>

                            <div style="display: inline-block; margin-right:10px">
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                    width="16" />
                                {{ $representative['phone'] }}
                            </div>

                            <div>
                                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}"
                                    width="16" />
                                <strong>Correspondence address:</strong> {{ $representative['address'] }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="spacer"></div>

@foreach ($representatives as $representativeIndex => $representative)
    @if (isset($representative['representatives']) && count($representative['representatives'] ?? []) > 0)
        <table>
            <h2>Representative {{ $representativeIndex }} Representation</h2>

            <tbody>
                <tr>
                    <td>
                        @if (isset($representative['representation']))
                            <div style="display: inline-block; margin-right:10px">
                                <strong>Representation:</strong> {{ $representative['representation'] }}
                            </div>
                        @endif

                        @if (isset($representative['application_status']))
                            <div style="display: inline-block; margin-right:10px">
                                <strong>Application status:</strong> {{ $representative['application_status'] }}
                            </div>
                        @endif

                        @if (isset($representative['authority']))
                            <div style="display: inline-block; margin-right:10px">
                                <strong>Authority:</strong> {{ $representative['authority'] }}
                            </div>
                        @endif
                    </td>
                </tr>

                @foreach ($representative['representatives'] as $representationRepresentativeIndex => $representationRepresentative)
                    <x-pdf-overview.person.representation.representative :representationRepresentative="$representationRepresentative" :index="$representationRepresentativeIndex" />
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach
