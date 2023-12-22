@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($dobIcon = file_get_contents(resource_path('img/pdf-icons/calendar.svg')))
@php($occupationIcon = file_get_contents(resource_path('img/pdf-icons/avatar.svg')))
@php($nationalInsuranceIcon = file_get_contents(resource_path('img/pdf-icons/document_protected.svg')))

<div class="panel panel-filled">
    @if ($type === 'Company')
        <div style="position: relative">
            <div style="display: inline-block; font-size: 12px">
                <strong>Company Name: {{ $name }}</strong>
            </div>
            <div style="font-size: 9px; position: absolute; top: 0px; right: 0px">
                <strong>Company Number: {{ $company_number }}</strong>
            </div>
        </div>
    @else
        <div style="font-size: 12px">
            <strong>Name: {{ $name }}</strong>
        </div>
    @endif

    @if ($name_change || $name_change_proof)
        <div>
            <div style="display: inline-block; margin-right:10px">
                <strong>Name change:</strong> {{ $name_change }}
            </div>
            @if ($name_change_reason)
                <div style="display: inline-block; margin-right:10px">
                    <strong>Reason for name change:</strong> {{ $name_change_reason }}
                </div>
            @endif
            <div style="display: inline-block; margin-right:10px">
                <strong>Proof of name change:</strong> {{ $name_change_proof ?? 'N/A' }}
            </div>
        </div>
    @endif

    @if ($type === 'Company')
        <div>
            <div style="display: inline-block; margin-right:10px">
                <strong>VAT status:</strong> {{ $vat_status }}
            </div>
            <div style="display: inline-block; margin-right:10px">
                <strong>VAT number:</strong> {{ $vat_number }}
            </div>
        </div>
    @else
        <div>
            @if ($date_of_birth)
                <div style="display: inline-block; margin-right:10px">
                    {!! $dobIcon !!}<strong>Date of birth:</strong> {{ $date_of_birth }}
                </div>
            @endif

            @if ($occupation)
                <div style="display: inline-block; margin-right:10px">
                    {!! $occupationIcon !!}<strong>Occupation:</strong> {{ $occupation }}
                </div>
            @endif

            @if ($national_insurance)
                <div style="display: inline-block; margin-right:10px">
                    {!! $nationalInsuranceIcon !!}<strong>National Insurance:</strong> {{ $national_insurance }}
                </div>
            @endif
        </div>
    @endif

    @if ($status)
        <div style="font-size: 9px">
            <strong>Status</strong>: {{ $status }}
        </div>
    @endif


    <div style="font-size: 10px">
        <strong>Contact details:</strong>
    </div>

    <div>
        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
        <strong>Correspondence address:</strong> {{ $address }}
    </div>

    <div>
        @if ($email)
            <div style="display: inline-block; margin-right:10px">
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                {{ $email }}
            </div>
        @endif

        @if ($phone)
            <div style="display: inline-block; margin-right:10px">
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                <strong>Main:</strong> {{ $phone }}
            </div>
        @endif

        @if ($phone_alt)
            <div style="display: inline-block; margin-right:10px">
                <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                <strong>Alt:</strong> {{ $phone_alt }}
            </div>
        @endif
    </div>

    @if ($post_completion_address)
        <div>
            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
            <strong>Post correspondence address:</strong> {{ $post_completion_address }}
        </div>
    @endif
</div>
