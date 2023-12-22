@component('mail::message')

<h1>{{ $title }}</h1>

<p class='email-summary'>{{ $description }}</p>

<a href="{{ $href }}" class="button-primary appearance-none">{{ $hrefText }}</a>

<p class='email-link'>Already have an account? <a class="text-primary" href={{ config('app.local_url') . '/login' }}>Login</a></p>

<hr>
@endcomponent
