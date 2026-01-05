@component('mail::message')
{{-- Greeting --}}
@if (! empty($msg['salutation']))
 {{$msg['salutation']}}
@else
# Hello!
@endif




{{ $msg['emailmessage'] }}





Thanks,<br>
{{ config('app.name') }}
@endcomponent
