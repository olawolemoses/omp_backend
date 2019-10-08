@component('mail::message')
# Password Reset

You are receiving this mail because you requested for a password reset. Follow the link below to perform this sacred duty.

@component('mail::button', ['url' => config('app.url') . "/password-reset/{$user->email}/{$user->remember_token}"])
    Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}

#### Please ignore this mail if you did not make this request.
@endcomponent
