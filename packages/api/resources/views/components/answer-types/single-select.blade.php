@props(['options', 'value'])

@php($tickPlain = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-plain.png'))))
@php($tickSelected = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/tick-selected.png'))))

<table class="no-border">
  <tr>
    @foreach ($options as $option)
      <td>
        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $option->value === $value ? $tickSelected : $tickPlain }}" width="16" />
        {{ $option->value }}
      </td>
    @endforeach
  </tr>
</table>
