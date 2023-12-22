@component('mail::message', ['to' => $to])

# Hi {{ $to->full_name}}

**{{ $currentUser->first_name }} {{ $currentUser->last_name }}** with **{{ $conveyancer->name }}** has invited you to complete your onboarding for the sale of {{ $address->single_line }}. Use the button below to set up your account and get started: 

@component('mail::button', [
    'url' => config('app.local_url') . '/register-client?token=' . $inviteCode . '&id=' . $to->id . '&email=' . $to->email,
    'color' => 'primary'
])
Accept Invitation
@endcomponent

@component('mail::subcopy')
Already have an account? [Log in]({{ config('app.local_url') }})
@endcomponent

@endcomponent
