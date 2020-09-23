@component('mail::message')
    User with email <b>{{ $user->email }}</b> has been created with ID <b>{{ $user->id }}</b>.
@endcomponent
