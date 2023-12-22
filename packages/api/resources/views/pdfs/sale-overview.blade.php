@php($emailIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/email.png'))))
@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))

<html>

<head>
    <style>
        {{ file_get_contents(resource_path('css/form-pdfs.css')) }}
    </style>
</head>

<body>
    <div id="header">
        <h3 style="float: left; margin-top: 20px;" class="text-primary">www.proconvey.co.uk</h3>
        <img style="float: right; margin-top: 20px; margin-right: 15px;"
            src="data:image/png;base64,{{ base64_encode(file_get_contents(resource_path('img/logo.png'))) }}"
            height="50px" />
    </div>

    <div id="footer">
        <table class="no-border">
            <tr>
                <td>
                    &copy; ProConvey Limited {{ date('Y') }}
                </td>
                <td style="text-align: right;">
                    ProConvey - {{ $property->type }} Overview
                    <span style="margin: 0 20px;">|</span>
                    Page <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <h1>{{ $property->type }} Overview</h1>

    <div class="spacer"></div>

    <x-pdf-overview.overview :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.overview-details :property="$property" :details="$details" :allSteps="$allSteps" />

    <div class="spacer"></div>

    <x-pdf-overview.the-buyers :property="$property" :details="$details" />

    @foreach ($details['people'] as $personIndex => $person)
        <x-pdf-overview.person :person="$person" :personIndex="$personIndex" :property="$property" />
    @endforeach

    <div class="spacer"></div>

    <x-pdf-overview.the-sellers :property="$property" :details="$details" :allSteps="$allSteps" />

    <div class="spacer"></div>

    <x-pdf-overview.client-bank-details :property="$property" :details="$details" :allSteps="$allSteps" />

    <div class="spacer"></div>

    <x-pdf-overview.sdlt-general :property="$property" :details="$details" :allSteps="$allSteps" />

    <div class="spacer"></div>

    <x-pdf-overview.sdlt-client-declaration :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.further-information :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.current-ownership :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.purchase-funds :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.purchase-funds.mortgages :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.purchase-funds.savings :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.purchase-funds.loans :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.purchase-funds.giftors :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.purchase-funds.purchase-funds-other :property="$property" :details="$details" />

    <div class="spacer"></div>

    <x-pdf-overview.mortgages-and-charges :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.related-transactions :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.client-bank-details :property="$property" :details="$details" :allSteps="$allSteps" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.sdlt-general :property="$property" :details="$details" :allSteps="$allSteps" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.sdlt-client-declaration :property="$property" :details="$details" />
    
    <div class="spacer"></div>
    
    <x-pdf-overview.further-information :property="$property" :details="$details" />

    <div class="spacer"></div>

    <table>
        @foreach ($allSteps?->firstWhere(fn($step) => $step->type === StepType::OwnerName)->getCompiledAnswer($property) as $ownerIndex => $owner)
            <thead>
                <tr>
                    <th @style(['border-radius: 0; border-top: none' => !$loop->first])>
                        <span class="text-primary">Owner {{ $ownerIndex + 1 }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
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
                                @if ($ownerExtraInformation[$ownerIndex]['email'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                            width="16" />
                                        {{ $ownerExtraInformation[$ownerIndex]['email'] }}
                                    </div>
                                @endif

                                @if ($ownerExtraInformation[$ownerIndex]['phone'])
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                            width="16" />
                                        {{ $ownerExtraInformation[$ownerIndex]['phone'] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>

    <div class="spacer"></div>

    <table>
        <h2 style="margin-top: 8px">The Buyers</h2>

        <thead></thead>
        <tbody>
            <tr>
                <td>
                    <div>
                        <strong>Conveyancer firm:</strong> {{ $property->conveyancer->name }}
                    </div>

                    <div>
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                        {{ $property->conveyancer->address->single_line }}
                    </div>
                </td>
            </tr>
        </tbody>
        @foreach ($allSteps?->firstWhere(fn($step) => $step->type === StepType::Buyer)->getCompiledAnswer($property) as $buyerIndex => $buyer)
            <thead>
                <tr>
                    <th>
                        Buyer {{ $buyerIndex + 1 }}
                    </th>
                </tr>
            </thead>

            <tbody>
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
                        </div>
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>


    @foreach ($allSteps?->firstWhere(fn($step) => $step->type === StepType::OwnerName)->getCompiledAnswer($property) as $ownerIndex => $owner)


        <div class="panel panel-filled">
            <div style="font-size: 12px">
                <strong>Name: {{ $owner['name'] }}</strong>
            </div>



            @if ($owner['type'] === 'Individual')
                <div style="font-size: 9px">
                    <strong>Status</strong>: {{ $ownerExtraInformation[$ownerIndex]['representation'] }}
                </div>

                <div style="font-size: 10px">
                    <strong>Contact details:</strong>
                </div>

                <div style="display: inline-block; margin-right:10px">
                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                    <strong>Correspondence address:</strong> {{ $owner['address'] }}
                </div>

                <div>
                    @if ($owner['phone'])
                        <div style="display: inline-block; margin-right:10px">
                            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                width="16" />
                            {{ $owner['phone'] }}
                        </div>
                    @endif

                    @if ($owner['email'])
                        <div style="display: inline-block; margin-right:10px">
                            <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                width="16" />
                            {{ $owner['email'] }}
                        </div>
                    @endif
                </div>

                <div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Name change:</strong> {{ $ownerExtraInformation[$ownerIndex]['name_change'] }}
                    </div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Reason for name change:</strong>
                        {{ $ownerExtraInformation[$ownerIndex]['name_change_reason'] }}
                    </div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Proof of name change:</strong>
                        {{ $ownerExtraInformation[$ownerIndex]['name_change_proof'] }}
                    </div>
                </div>
            @else
                <div>
                    <div style="display: inline-block">
                        <strong>Company number:</strong> {{ $ownerExtraInformation[$ownerIndex]['company_number'] }}
                    </div>
                    <div style="display: inline-block">
                        <strong>Status</strong>: {{ $ownerExtraInformation[$ownerIndex]['representation'] }}
                    </div>
                </div>

                <div style="font-size: 10px">
                    <strong>Company contact details:</strong>
                </div>

                <div style="display: inline-block; margin-right:10px">
                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
                    <strong>Address:</strong> {{ $ownerExtraInformation[$ownerIndex]['address'] }}
                </div>

                <div>
                    <div style="display: inline-block; margin-right:10px">
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
                        {{ $ownerExtraInformation[$ownerIndex]['phone'] }}
                    </div>

                    <div style="display: inline-block; margin-right:10px">
                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}" width="16" />
                        {{ $ownerExtraInformation[$ownerIndex]['email'] }}
                    </div>
                </div>

                <div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Name change:</strong> {{ $ownerExtraInformation[$ownerIndex]['name_change'] }}
                    </div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>Proof of name change:</strong>
                        {{ $ownerExtraInformation[$ownerIndex]['name_change_proof'] }}
                    </div>
                </div>

                <div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>VAT status:</strong> {{ $ownerExtraInformation[$ownerIndex]['vat_status'] }}
                    </div>
                    <div style="display: inline-block; margin-right:10px">
                        <strong>VAT number:</strong> {{ $ownerExtraInformation[$ownerIndex]['vat_number'] }}
                    </div>
                </div>
            @endif
        </div>

        @if ($owner['type'] === 'Individual')
            @if (count($ownerExtraInformation[$ownerIndex]['representatives']) > 0)
                <table>
                    <h2>Owner Representation</h2>

                    <thead></thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Representation:</strong>
                                    {{ $ownerExtraInformation[$ownerIndex]['representation'] }}
                                </div>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Application status:</strong>
                                    {{ $ownerExtraInformation[$ownerIndex]['application_status'] }}
                                </div>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Authority:</strong> {{ $ownerExtraInformation[$ownerIndex]['authority'] }}
                                </div>
                            </td>
                        </tr>
                    <tbody>

                        @foreach ($ownerExtraInformation[$ownerIndex]['representatives'] as $ownerRepresentativeIndex => $ownerRepresentative)
                            <thead>
                                <tr>
                                    <th>
                                        Representative {{ $ownerRepresentativeIndex + 1 }}
                                    </th>
                                </tr>
                            </thead>

                    <tbody>
                        <tr>
                            <td>
                                <div style="font-size: 12px">
                                    <strong>{{ $ownerRepresentative['name'] }}</strong>
                                </div>

                                <div>
                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                            width="16" />
                                        {{ $ownerRepresentative['email'] }}
                                    </div>

                                    <div style="display: inline-block; margin-right:10px">
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                            width="16" />
                                        {{ $ownerRepresentative['phone'] }}
                                    </div>

                                    <div>
                                        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}"
                                            width="16" />
                                        <strong>Correspondence address:</strong> {{ $ownerRepresentative['address'] }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
            @endforeach
            </table>
        @endif
    @else
        <table>
            <h2>Company Representation</h2>

            <thead></thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Company Representation:</strong>
                        {{ $ownerExtraInformation[$ownerIndex]['representation'] }}
                    </td>
                </tr>
            </tbody>

            @foreach ($ownerExtraInformation[$ownerIndex]['representatives'] as $companyRepresentativeIndex => $companyRepresentative)
                <thead>
                    <tr>
                        <th>
                            Company Representative {{ $companyRepresentativeIndex + 1 }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <div style="font-size: 18px">
                                <strong>{{ $companyRepresentative['name'] }}</strong>
                            </div>

                            <div>
                                <div style="display: inline-block; margin-right:10px">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $emailIcon }}"
                                        width="16" />
                                    {{ $companyRepresentative['email'] }}
                                </div>

                                <div style="display: inline-block; margin-right:10px">
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}"
                                        width="16" />
                                    {{ $companyRepresentative['phone'] }}
                                </div>

                                <div>
                                    <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}"
                                        width="16" />
                                    <strong>Correspondence address:</strong> {{ $companyRepresentative['address'] }}
                                </div>

                                <div>
                                    <strong>Status:</strong> {{ $companyRepresentative['representation'] }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>

        @foreach ($ownerExtraInformation[$ownerIndex]['representatives'] as $companyRepresentativeIndex => $companyRepresentative)
            @if (count($companyRepresentative['representatives']) > 0)
                <table>
                    <h2>Representative {{ $companyRepresentative['name'] }} Representation</h2>

                    <thead></thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Representation:</strong> {{ $companyRepresentative['representation'] }}
                                </div>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Application status:</strong>
                                    {{ $companyRepresentative['application_status'] }}
                                </div>
                                <div style="display: inline-block; margin-right:10px">
                                    <strong>Authority:</strong> {{ $companyRepresentative['authority'] }}
                                </div>
                            </td>
                    </tbody>


                    @foreach ($companyRepresentative['representatives'] as $companyRepresentativeRepresentativeIndex => $companyRepresentativeRepresentative)
                        <thead>
                            <tr>
                                <th>
                                    Representative {{ $companyRepresentativeRepresentativeIndex + 1 }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-size: 18px">
                                        <strong>{{ $companyRepresentativeRepresentative['name'] }}</strong>
                                    </div>

                                    <div>
                                        <div style="display: inline-block; margin-right:10px">
                                            <img style="margin-bottom: -4px; margin-right: 4px"
                                                src="{{ $emailIcon }}" width="16" />
                                            {{ $companyRepresentativeRepresentative['email'] }}
                                        </div>

                                        <div style="display: inline-block; margin-right:10px">
                                            <img style="margin-bottom: -4px; margin-right: 4px"
                                                src="{{ $phoneIcon }}" width="16" />
                                            {{ $companyRepresentativeRepresentative['phone'] }}
                                        </div>

                                        <div>
                                            <img style="margin-bottom: -4px; margin-right: 4px"
                                                src="{{ $pinIcon }}" width="16" />
                                            <strong>Correspondence address:</strong>
                                            {{ $companyRepresentativeRepresentative['address'] }}
                                        </div>
                                    </div>

                                    <div>
                                        <div style="display: inline-block; margin-right:10px">
                                            <strong>Name change:</strong>
                                            {{ $companyRepresentativeRepresentative['name_change'] }}
                                        </div>
                                        <div style="display: inline-block; margin-right:10px">
                                            <strong>Reason for name change:</strong>
                                            {{ $companyRepresentativeRepresentative['name_change_reason'] }}
                                        </div>
                                        <div style="display: inline-block; margin-right:10px">
                                            <strong>Proof of name change:</strong>
                                            {{ $companyRepresentativeRepresentative['name_change_proof'] }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            @endif
        @endforeach
    @endif
    @endforeach

    <div class="page-break"></div>

    <h1>Mortgages and Related Transactions</h1>

    <div class="spacer"></div>

    <table>
        <h2>Mortgages</h2>

        <thead>
            <tr>
                <th>Chargee</th>
                <th>Account number</th>
                <th>Approx. amount outstanding</th>
                <th>Early repayment charge</th>
                <th>Approx. repayment charge</th>
            </tr>
        </thead>
        <tbody>
            @forelse (collect($allSteps?->firstWhere(fn ($step) => $step->type === StepType::Charges)->getCompiledAnswer($property))->where('type', 'Mortgage') as $charge)
                <tr>
                    <td>{{ $charge['chargee'] }}</td>
                    <td>{{ $charge['account_number'] }}</td>
                    <td>{{ $charge['amount_outstanding'] }}</td>
                    <td>{{ $charge['early_repayment_charge'] }}</td>
                    <td>{{ $charge['approx_repayment_charge'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center">No mortgages</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="spacer"></div>

    <table>
        <h2>Charges or Loans</h2>

        <thead>
            <tr>
                <th>Name of chargee/loanee</th>
                <th>Approx. amount outstanding</th>
            </tr>
        </thead>
        <tbody>
            @forelse (collect($allSteps?->firstWhere(fn ($step) => $step->type === StepType::Charges)->getCompiledAnswer($property))->where('type', '!=', 'Mortgage') as $charge)
                <tr>
                    <td>{{ $charge['chargee'] }}</td>
                    <td>{{ $charge['amount_outstanding'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center">No charges or loans</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
