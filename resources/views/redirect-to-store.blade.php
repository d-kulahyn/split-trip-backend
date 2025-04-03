@extends('app')


@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const device = navigator.userAgent;
            const isAndroid = device.match(/Android/i);
            const isIos = device.match(/iPhone|iPad|iPod/i);

            if (isAndroid) {
                //go to google play store
                window.location.href = 'market://details?id=com.einnovation.temu';

            }

            if (isIos) {
                //go to app store
                window.location.href = 'https://apps.apple.com/us/app/splitwise/id458023433';
            }
        })
    </script>
@endsection
