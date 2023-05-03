<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" referrerpolicy="no-referrer" />
        <!--Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&display=swap">
        <script src="https://cdn.tailwindcss.com/3.2.4"></script>
        <script src="https://unpkg.com/verte@0.0.12/dist/verte.js"></script>
        <link id="theme-link" rel="stylesheet" href="/themes/light-mode/theme.css">
        <link rel="stylesheet" href="/themes/primevue.min.css">
        <link rel="stylesheet" href="/themes/primevue.css">
        <title>Admin Portal | Rocket Flood</title>
        @vite('resources/css/app.css')
    </head>
    <body>
        <div id="app"></div>
        @vite('resources/js/app.js')
    </body>
</html>