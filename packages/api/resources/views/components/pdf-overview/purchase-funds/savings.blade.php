<table>
    <h2>Savings</h2>

    <tbody>
        <tr class="table-header">
            <td>
                Buyer
            </td>
            <td>
                Amount
            </td>
        </tr>

        @foreach ($buyerSavings as $buyerSavingIndex => $buyerSaving)
            <tr>
                <td>
                    <div>
                        {{ $buyerSaving['buyer']['name'] }}
                    </div>
                </td>
                <td>
                    <div>
                        £{{ $buyerSaving['saving'] }}
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
