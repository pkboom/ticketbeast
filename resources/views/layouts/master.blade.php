<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'TicketBeast')</title>

        <link rel="stylesheet" href="/css/main.css">
        @include('scripts.app')
    </head>
    <body class="bg-grey-lighter">
        <div id="app">
            @yield('body')
        </div>

        @stack('beforeScripts')
        <script src="/js/app.js"></script>
        @stack('afterScripts')
    </body>
</html>