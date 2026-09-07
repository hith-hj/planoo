<!DOCTYPE html>
<html lang="en" dir="{{app()->getLocale() === 'ar'? 'rtl':'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planoo | Get Better</title>
    <link rel="icon" type="text/css" href="images/user-logo-new.jpeg">
    <link rel="icon" type="text/css" href="images/partner-logo-new.jpeg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="PLANOO is a smart platform designed to simplify how you discover, book, and manage services in one place. Easily find sports courses, courts, and unique experiences.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.planoo.net/">

    <!-- 3. Social Media Optimization (Open Graph for Facebook & LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.planoo.net/">
    <meta property="og:title" content="Planoo | Get Better - Discover & Book Local Activities">
    <meta property="og:description" content="Simplify how you manage your lifestyle. Book sports courts, join fitness courses, and explore local experiences with PLANOO.">
    <meta property="og:image" content="https://planoo.net">

    <!-- 4. Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://www.planoo.net/">
    <meta name="twitter:title" content="Planoo | Get Better - Discover & Book Local Activities">
    <meta name="twitter:description" content="Simplify how you manage your lifestyle. Book sports courts, join fitness courses, and explore local experiences with PLANOO.">
    <meta name="twitter:image" content="https://planoo.net">

    <!-- Favicon Links -->
    <link rel="icon" href="https://planoo.net" type="image/x-icon">
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
