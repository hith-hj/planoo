<footer id="contact" class="relative isolate overflow-hidden border-t border-line bg-soft pt-16 text-ink">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <img src="{{ asset('images/hero-bg.avif') }}" alt="" loading="lazy" class="h-full w-full object-cover object-center">
        <div class="absolute inset-0 bg-soft/85 dark:bg-soft/90"></div>
    </div>
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="flex flex-col items-start justify-between gap-8 border-b border-line pb-12 sm:flex-row sm:items-center [&_h2]:text-[2.6rem] [&_p]:mt-3.5 [&_p]:max-w-[500px] [&_p]:text-[.9rem] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('LET’S TALK') }}</span>
<h2>{{ __('Any question?') }}</h2>
<p>{{ __('If you have any question don’t hesitate, you can ask anything you want.') }}</p>
</div>
<x-button href="mailto:info@planoo.net">{{ __('Contact us') }} <i class="bi bi-arrow-up-right" aria-hidden="true">
</i>
</x-button>
</div>
<div class="grid grid-cols-2 gap-x-5 gap-y-8 py-12 sm:grid-cols-[2fr_1fr_1fr] sm:gap-8 min-[901px]:gap-[70px] [&>div:first-child]:col-span-2 sm:[&>div:first-child]:col-span-1 [&_p]:mt-5 [&_p]:max-w-[330px] [&_p]:text-[.85rem] [&_h3]:mb-[18px] [&_h3]:text-[.85rem] [&>div>a:not([data-brand])]:block [&>div>a:not([data-brand])]:py-[5px] [&>div>a:not([data-brand])]:text-[.8rem] [&>div>a:not([data-brand])]:text-muted [&>div>a:not([data-brand]):hover]:text-accent">
<div>
<x-brand />
<p>{{ __('Discover, book, and make time for the things you love. Your next activity starts here.') }}</p>
</div>
<div>
<h3>{{ __('Discover') }}</h3>
<a href="{{ route('home') }}#sections">{{ __('Activities') }}</a>
<a href="{{ route('home') }}#kids">{{ __('Activities For Kids') }}</a>
<a href="{{ route('home') }}#apps">{{ __('Apps') }}</a>
</div>
<div>
<h3>{{ __('Company') }}</h3>
<a href="{{ route('about') }}">{{ __('About us') }}</a>
<a href="{{ route('home') }}#partners">{{ __('Become Partner') }}</a>
<a href="mailto:info@planoo.net">info@planoo.net</a>
</div>
</div>
<div class="flex flex-wrap justify-between gap-5 border-t border-line py-[22px] text-[.7rem] text-muted [&>div]:flex [&>div]:gap-6 [&_a:hover]:text-accent">
<span>© {{ date('Y') }} Planoo. {{ __('All rights reserved.') }}</span>
<div>
<a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a>
<a href="{{ route('terms') }}">{{ __('Terms & conditions') }}</a>
</div>
<a href="#main-content">{{ __('Back to top') }} ↑</a>
</div>
</div>
</footer>



