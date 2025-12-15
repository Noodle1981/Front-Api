@component('mail::message')
# {{ $step->subject }}

{!! $step->body !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
