<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<img src="{{ asset('img/logo.png') }}" class="footer-logo" alt="" />

© {{ date('Y') }} ProConvey Limited. All rights reserved.

This email was sent to <strong>{{ is_string($to) ? $to : $to->email }}</strong> to update you about important information regarding your ProConvey account.

</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
