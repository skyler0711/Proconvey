<html>
  <head>
    <style>
      {{ file_get_contents(resource_path('css/form-pdfs.css')) }}
    </style>
  </head>

  <body>
    <div id="header">
      <h3 style="float: left; margin-top: 20px;" class="text-primary">www.proconvey.co.uk</h3>
      <img style="float: right; margin-top: 20px; margin-right: 15px;" src="data:image/png;base64,{{ base64_encode(file_get_contents(resource_path('img/logo.png'))) }}" height="50px" />
    </div>

    <div>
      <table class="no-border">
        <tr>
          <td></td>
          <td style="text-align: right;">
            <p>
              {{ $name }}<br/>
              {{ $address_line_1 }}<br/>
              @if ($address_line_2)
                {{ $address_line_2 }}<br/>
              @endif
              {{ $address_city }}<br/>
              {{ $address_postcode }}<br/>
            </p>
            <p>{{ $date }}</p>
          </td>
        </tr>
      </table>
    </div>

    <h1>Gifted Deposit Declaration</h1>

    <p>
      <b>Case Reference: </b>{{$reference}}
    </p>

    <p>{{ $property_address }}</p>

    <p>
      I, {{ $name }}, confirm that I am gifting the balance of £{{ $amount }} to {{ $buyers }} in order to assist them with their purchase of {{ $property_address }}.
    <p>

    <p>
      I further confirm:
    </p>

    <p>
      1. The balance is being gifted as a sign of my natural love and affection.<br/>
      2. I will in no way have any interest in {{ $property_address }}.<br/>
      3. The gifted balance is not repayable in any way whatsoever.<br/>
    <p>

    <div class="giftor-declaration-sign-box">
      <p>
        Sign: _______________________________
      </p>

      <p>
        Date: {{ $date }}
      </p>

      <p>
        Name: {{ $name }}
      </p>
    </div>

    <div id="footer">
      <table class="no-border">
        <tr>
          <td>
            &copy; ProConvey Limited {{ date('Y') }}
          </td>
          <td style="text-align: right;">
            ProConvey
            <span style="margin: 0 20px;">|</span>
            Page <span class="page-number"></span>
          </td>
        </tr>
      </table>
    </div>

    <br />

  </body>
</html>
