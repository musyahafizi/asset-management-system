<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.css') }}">

    <!-- CUSTOM -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>

    @yield('content')

    <!-- JQUERY -->
    <script src="{{ asset('assets/js/jquery-1.10.2.js') }}"></script>

    <!-- BOOTSTRAP -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    <!-- CUSTOM -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>
</html>
