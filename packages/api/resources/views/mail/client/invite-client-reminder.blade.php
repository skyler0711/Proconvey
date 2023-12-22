@isset($conveyancer->logo_image)
<div style="text-align: center; margin-top: 45px; margin-bottom: 55px;">
  <img src="{{ $conveyancer->logo_image->original_url }}" alt="{{ $conveyancer->name }}" width="260px" />
</div>
@endisset

@component('mail::message', ['to' => $to])


# Hi {{ $to->full_name}}
This is a friendly reminder to login and complete your conveyancing tasks for **{{ $address->single_line }}**. Use the button below to set up your account and get started:

@component('mail::button', [
'url' => config('app.local_url') . '/register-client?token=' . $inviteCode . '&id=' . $to->id . '&email=' . $to->email,
'color' => 'primary'
])
Get started
@endcomponent

@component('mail::subcopy')
Already have an account? [Log in]({{ config('app.local_url') }})
@endcomponent

@endcomponent
