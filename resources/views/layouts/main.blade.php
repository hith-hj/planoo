<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#462e8e">
    <script>
        (() => {
            let theme;
            document.documentElement.dataset.motion = 'full';
            try { theme = localStorage.getItem('planoo-theme'); } catch {}
            document.documentElement.dataset.theme = theme === 'light' || theme === 'dark'
                ? theme : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    <title>@yield('title', __('Discover your next activity')) | Planoo</title>
    <meta name="description" content="{{ __('Discover sports courts, activities, courses and experiences with Planoo. Find your next way to get better.') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/user-logo-new.jpeg') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', __('Discover your next activity')) | Planoo">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/hero-bg.avif') }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <a class="fixed -top-[100px] start-5 z-[100] border-2 border-accent bg-surface px-6 py-3 focus:top-3" href="#main-content">{{ __('Skip to content') }}</a>
    @include('partials.nav')
    <main id="main-content" tabindex="-1">@yield('content')</main>
    @include('partials.footer')
</body>
</html>
