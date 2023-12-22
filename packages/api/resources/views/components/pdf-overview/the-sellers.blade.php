@php($phoneIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/phone.png'))))
@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))

<div class="page-break"></div>

<table>
  <h2>The Sellers</h2>

  <tbody>
    <tr class="table-header">
      <td>
        Sellers Solicitor
      </td>
    </tr>
  </tbody>
    <tr>
      <td>
        <div>
          <strong>{{ $property->conveyancer->name }}</strong>
        </div>

        <div>
          <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="16" />
          {{ $property->conveyancer->address->single_line }}
        </div>
        <div>
          <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $phoneIcon }}" width="16" />
          {{ $solicitor['phone']}}
        </div>
      </td>
    </tr>
  </tbody>
</table>

<div class="spacer"></div>

<table>
  <thead>
    @foreach ($sellers as $sellerindex => $seller)
    <tr>
      <th>
        Seller {{ $sellerindex + 1 }}
      </th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        <div>
          {{ $seller['name'] }}
        </div>
        @if (isset($seller['company_number']))
        <div class="display: inline-block;">
          Company number: {{ $seller['company_number'] }}
        </div>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
