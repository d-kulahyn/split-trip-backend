@component('mail::message')
    You need to pay: {{ $debtAmount }} {{ $debtCurrency }} to {{$debtorName}} in {{$groupName}}.
@endcomponent
