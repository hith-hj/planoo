<!DOCTYPE html>
<html lang="en" dir="{{app()->getLocale() === 'ar'? 'rtl':'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planoo | Get Better</title>
    <link rel="icon" type="text/css" href="images/user-logo-new.jpeg">
    <link rel="icon" type="text/css" href="images/partner-logo-new.jpeg">
    @vite(['resources/js/app.js','resources/css/app.css'])
</head>
<body class="font-sans text-dark antialiased bg-white rtl:text-2xl">
    <!-- MAIN HEADER -->
    @include('partials.nav')

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <section id="contact" class="">
        @include('partials.footer')
    </section>
</body>
</html>
