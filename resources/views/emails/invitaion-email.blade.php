@component('mail::message')
# Invitation

You're invited to promote your concerts on TicketBeast!

@component('mail::button', ['url' => url("/invitations/{$invitation->code}")])
Visit this link to create your accout:
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
