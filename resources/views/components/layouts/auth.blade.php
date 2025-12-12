<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login DigiKampus UT' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/globalFont.css') }}">
    @vite('resources/css/app.css')
</head>

<body>
    {{ $slot }}
</body>

</html>
