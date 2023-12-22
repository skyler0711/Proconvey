@props(['address'])

{{ $address['line_1'] }}<br />

@if (array_key_exists('line_2', $address))
  {{ $address['line_2'] }}<br />
@endif

{{ $address['city'] }}<br />

{{ $address['postcode'] }}
