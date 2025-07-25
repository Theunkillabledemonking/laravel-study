<!-- resources/views/layouts/master.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>웹사이트 | @yield('title', 'Home page')</title>
</head>
<body>
    <div class="container">@yield('content')</div>
    @section('footerScripts')
        <script src="app.js"></script>
    @show
</body>
</html>