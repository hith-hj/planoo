@extends('layouts.main')
@section('title', __('About us'))
@section('content')
<section class="bg-[#f3f0f9] py-[52px] text-center sm:py-[76px] dark:bg-[#251e35] [&_h1]:text-[clamp(2.5rem,5vw,4rem)] [&_h1]:text-accent [&_p]:mx-auto [&_p]:mt-[22px] [&_p]:max-w-[650px]">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('GET TO KNOW PLANOO') }}</span>
<h1>{{ __('Better, together.') }}</h1>
<p>{{ __('We are dedicated to creating vibrant spaces where health, learning, and community come together.') }}</p>
</div>
</section>
<section class="py-[60px] sm:py-24 mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="grid grid-cols-1 items-center gap-10 sm:grid-cols-2 min-[1051px]:gap-20 [&_p]:mt-[22px] [&_p]:mb-7 [&_em]:block transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100 mb-16 [&>img]:h-[360px] [&>img]:w-full [&>img]:rounded-[20px]" data-reveal>
<img src="{{ asset('images/activity.avif') }}" alt="{{ __('Sports and shared experiences with Planoo') }}" width="680" height="500">
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('About Us') }}</span>
<h2>{{ __('More ways to live better.') }}</h2>
<p>{{ __('Our mission is to inspire people of all ages to live better, healthier, and more connected lives.') }}</p>
<x-button href="{{ route('home') }}#sections" >{{ __('Explore activities') }} ↗</x-button>
</div>
</div>@include('partials.values')</section>
@include('partials.apps')
@endsection
