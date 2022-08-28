@component('mail::message')
# Gentile <strong>{{ $user->name }}</strong>

Benvenuto sul mio sito.

Per accedere al tuo account, clicca anche il link qui sotto:

@component('mail::button', ['url' => route("login")])
Button Text
@endcomponent

Cordiali Saluti,<br>
Paolo Pugliese
@endcomponent
