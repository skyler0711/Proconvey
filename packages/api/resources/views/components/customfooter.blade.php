<p>
<img class="small-logo" src="{{ asset('img/logo.png') }}"/>
</p>
<p>© {{ date('Y') }} ProConvey Limited. All rights reserved.</p>
<p>{{ config('app.address') }}</p>
<br>
<p>This email was sent to <strong>{{ $to->email }}</strong> to update you about important information regarding
    your ProConvey account.</p>
</div>
