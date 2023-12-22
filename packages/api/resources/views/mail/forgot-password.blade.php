@component('mail::message', ['to' => $to])

# Hi {{ $to->email }}

Forgotten your password? No problem. Click the button below to reset it. 

@component('mail::button', [
    'url' => config('app.local_url') . '/reset-password?token=' . $token . '&email=' . urlencode($to->email),
    'color' => 'primary'
])
Reset Password
@endcomponent

@endcomponent
