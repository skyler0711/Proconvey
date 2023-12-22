@if ($property->type === 'Purchase')
    <div class="page-break"></div>

    <table>
        <h2>Further Information</h2>

        <tbody>
            <tr>
                <td class="table-header">
                    <span class="text-primary">Client additional information</span>
                </td>
            </tr>

            <tr>
                <td>
                    <div style="font-size: 18px">
                        Is there anything in particular you would like your lawyer to check regarding the property?
                    </div>
                    <div>
                        {{ $details['further_information'] }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
@endif
