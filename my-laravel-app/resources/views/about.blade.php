<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>About Us</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: Arial, sans-serif; }
            </style>
        @endif
    </head>
    <body>
        <div id="app"></div>

        <script>
            // Pass page data to Vue
            window.pageData = {
                currentPage: 'about',
                title: 'About project'
            };
        </script>
    </body>
</html>