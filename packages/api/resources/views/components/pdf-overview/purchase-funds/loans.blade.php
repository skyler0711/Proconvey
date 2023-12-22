<table>
    <h2>Loans</h2>

    <tbody>
        <tr class="table-header">
            <td>
                Loanee
            </td>
            <td>
                Amount
            </td>
        </tr>
        
        @foreach ($lenders as $lenderIndex => $lender)
            <tr>
                <td>
                    <div>
                        {{ $lender['name'] }}
                    </div>
                </td>
                <td>
                    <div>
                        £{{ $lender['loan_amount'] }}
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
