@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => url("/orders/{$order->confirmation_number}")])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
