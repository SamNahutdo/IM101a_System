<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FalconSystem — Athletic Resource Management')</title>
    <link rel="stylesheet" href="{{ asset('css/falcon.css') }}">
</head>
<body class="auth-page">
    @yield('content')
    <script src="{{ asset('js/falcon.js') }}"></script>
</body>
</html>
