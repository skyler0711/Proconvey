@props(['label', 'value'])

@php($tickPlain = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-plain.png'))))
@php($tickSelected = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-selected.png'))))

<table class="no-border">
  <tr>
    <td>
      <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $value === '1' ? $tickSelected : $tickPlain }}" width="16" />
      {{ $label }}
    </td>
  </tr>
</table>
