<div class="page-break"></div>

<h2>
    {{ $title }}
</h2>

<table>

    <h3>
        Mortgages
    </h3>

    <tbody>

        <tr class="table-header">

            <td>
                Chargee
            </td>

            <td>
                Account number
            </td>

            <td>
                Approx. amount outstanding
            </td>

            <td>
                Early repayment charge
            </td>

            <td>
                Approx. repayment charge
            </td>

        </tr>

        @if (count($mortgages) > 0)

            @foreach ($mortgages as $mortgage)
                <tr>

                    <td>
                        {{ $mortgage['chargee'] }}
                    </td>

                    <td>
                        {{ $mortgage['account_number'] }}
                    </td>

                    <td>
                        {{ $mortgage['amount_outstanding'] }}
                    </td>

                    <td>
                        {{ $mortgage['early_repayment_charge'] }}
                    </td>

                    <td>
                        {{ $mortgage['approx_repayment_charge' ] ?? 'N/a' }}
                    </td>

                </tr>
            @endforeach

        @endif

        @if (count($mortgages) === 0)
            <tr>

                <td>
                    No mortgages
                </td>

                <td></td>
                <td></td>
                <td></td>
                <td></td>

            <tr>
        @endif

    </tbody>

</table>

<table>

    <h3>
        Charges or Loans
    </h3>

    <tbody>

        <tr class="table-header">

            <td>
                Name of charge/loanee
            </td>

            <td>
                Approx. amount outstanding
            </td>

            <td>
                Type
            </td>

        </tr>

        @if (count($charges) > 0)

             @foreach ($charges as $charge)

                <tr>

                    <td>
                        {{ $charge['chargee'] }}
                    </td>

                    <td>
                        {{ $charge['amount_outstanding']}}
                    </td>

                    <td>
                        {{ $charge['type'] }}
                    </td>

                </tr>

             @endforeach

        @endif

        @if (count($loans) > 0)

            @foreach ($loans as $loan)

                <tr>

                    <td>
                        {{ $loan['chargee'] }}
                    </td>

                    <td>
                        {{ $loan['amount_outstanding']}}
                    </td>

                    <td>
                        {{ $loan['type'] }}
                    </td>

                </tr>

             @endforeach

        @endif

        @if (count($charges) === 0 && count($loans) === 0)
            <tr>

                <td>
                    No charges or loans
                </td>

                <td></td>
                <td></td>

            </tr>
        @endIf

    </tbody>

</table>

