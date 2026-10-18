@extends('layouts.main')
@section('title', __('Discover your next activity'))
@section('content')
<section id="home" class="overflow-hidden bg-soft">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px] grid grid-cols-1 items-center gap-9 py-12 sm:grid-cols-[1.05fr_1fr] sm:gap-8 min-[901px]:pt-[62px] min-[901px]:pb-[70px] min-[1051px]:gap-16">
<div class="animate-[soft-enter_.65s_ease-out_both] [&_h1]:mb-6 [&_h1]:text-[3.2rem] sm:[&_h1]:text-[clamp(3rem,5.3vw,4.65rem)] [&_h1]:leading-[1.07] [&_em]:block [&>p]:max-w-[420px] [&>p]:text-[1.05rem] [&>p]:leading-[1.8]">
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">
<span class="size-[7px] shrink-0 rounded-full bg-teal">
</span> {{ __('A LITTLE MORE MOVEMENT. A LOT MORE LIFE.') }}</span>
<h1>{{ __('Good days start') }} <em>{{ __('with a plan.') }}</em>
</h1>
<p>{{ __('Discover courts, try something new, and find your people. Make your next move with Planoo.') }}</p>
<div class="mt-8 mb-9 flex items-center gap-[22px] sm:gap-7">
<x-button  href="#sections">{{ __('Explore activities') }} <i class="bi bi-arrow-up-right" aria-hidden="true">
</i>
</x-button>
<x-text-link href="#apps">{{ __('Get the app') }} <i class="bi bi-arrow-down" aria-hidden="true">
</i>
</x-text-link>
</div>
<div class="flex items-center gap-[11px] text-[.73rem] text-muted">
<span class="inline-flex size-[38px] shrink-0 items-center justify-center rounded-full bg-[#e9f5ef] text-teal dark:bg-[#233e36]">
<i class="bi bi-heart" aria-hidden="true">
</i>
</span>
<span>{{ __('More ways to move. More reasons to get together.') }}</span>
</div>
</div>
<div class="relative pb-[18px] animate-[soft-enter_.75s_.08s_ease-out_both] [&>img]:h-[350px] [&>img]:w-full sm:[&>img]:h-[400px] min-[901px]:[&>img]:h-[470px] [&>img]:rounded-[120px_18px_18px_18px] rtl:[&>img]:rounded-[18px_120px_18px_18px]">
<img src="{{ asset('images/hero-bg.avif') }}" alt="{{ __('Discover an active lifestyle with Planoo') }}" fetchpriority="high" width="720" height="800">
<div class="absolute inset-x-[22px] bottom-0 flex items-center gap-3.5 rounded-[13px] border border-line bg-surface p-[18px] shadow-[0_10px_30px_#30204e0d] [&_strong]:block [&_strong]:text-[.88rem] [&_div>span]:block [&_div>span]:text-xs [&_div>span]:text-muted [&>i]:ms-auto [&>i]:text-accent">
<span class="inline-flex size-[38px] shrink-0 items-center justify-center rounded-full bg-[#e9f5ef] text-teal dark:bg-[#233e36]">
<i class="bi bi-lightning-charge-fill" aria-hidden="true">
</i>
</span>
<div>
<strong>{{ __('Your next experience') }}</strong>
<span>{{ __('Just a plan away.') }}</span>
</div>
<i class="bi bi-arrow-up-right" aria-hidden="true">
</i>
</div>
<span class="absolute top-[25px] end-5 rotate-[5deg] rounded-[30px] bg-yellow px-3.5 py-2 text-[.6rem] font-extrabold tracking-[.08em] text-[#30204e]">{{ __('GET OUT. JOIN IN. GET BETTER.') }}</span>
</div>
</div>
</section>
<div class="border-b border-line [&>div]:flex [&>div]:flex-wrap [&>div]:items-center [&>div]:gap-x-6 [&>div]:gap-y-3.5 [&>div]:py-6 min-[901px]:[&>div]:justify-between [&_span]:w-full min-[901px]:[&_span]:w-auto [&_span]:text-xs [&_span]:text-muted [&_a]:flex [&_a]:items-center [&_a]:gap-2.5 sm:[&_a]:gap-[22px] [&_a]:text-[.82rem] [&_a]:font-semibold [&_a:hover]:text-teal">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<span>{{ __('Find your kind of active') }}</span>@foreach(['Courts', 'Activities', 'Trainers', 'Courses', 'Events'] as $category)<a href="#sections">{{ __($category) }} <i class="bi bi-arrow-up-right" aria-hidden="true">
</i>
</a>@endforeach</div>
</div>
<section id="sections" class="py-[60px] sm:py-24 mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="mb-9 flex flex-col items-start justify-between gap-5 sm:flex-row sm:gap-9 min-[901px]:items-end [&>p]:text-[.9rem] sm:[&>p]:max-w-[280px] min-[901px]:[&>p]:max-w-[360px] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('MAKE TIME FOR YOU') }}</span>
<h2>{{ __('A world of things to do.') }}</h2>
</div>
<p>{{ __('From your favourite sport to your next new hobby. Find a little inspiration for your next plan.') }}</p>
</div>
<div class="mb-[30px] flex flex-wrap gap-[7px] sm:gap-2 [&_button]:cursor-pointer [&_button]:rounded-[25px] [&_button]:border [&_button]:border-line [&_button]:bg-surface [&_button]:px-3.5 [&_button]:py-2 [&_button]:text-xs sm:[&_button]:px-5 sm:[&_button]:py-[9px] sm:[&_button]:text-[.8rem] [&_button]:text-muted [&_button]:transition-colors [&_button:hover]:border-accent [&_button[aria-pressed=true]]:border-purple [&_button[aria-pressed=true]]:bg-purple [&_button[aria-pressed=true]]:text-white dark:[&_button[aria-pressed=true]]:bg-[#7050bd]" role="group" aria-label="{{ __('Filter activities') }}">@foreach(['all' => 'All activities', 'courts' => 'Courts', 'activities' => 'Activities', 'trainers' => 'Trainers', 'courses' => 'Courses', 'events' => 'Events'] as $key => $label)<button type="button" data-filter="{{ $key }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ __($label) }}</button>@endforeach</div>
<div class="grid grid-cols-1 gap-7 sm:grid-cols-2 min-[901px]:grid-cols-3">
        @foreach([
            ['courts', 'Courts', 'Wide range of courts', 'Discover indoor and outdoor courts for basketball, tennis, and more.', 'activity.avif', 'bi-dribbble'],
            ['activities', 'Activities', 'Swimming & Lessons', 'Enjoy recreational swimming, take lessons, or join a local swim club.', 'swimming.webp', 'bi-water'],
            ['trainers', 'Trainers', 'Expert trainers to guide you', 'Choose from certified trainers and book classes to reach your fitness goals.', 'booking.webp', 'bi-person-arms-up'],
            ['courses', 'Courses', 'Learn new skills with expert guidance', 'Explore a variety of courses from fitness to wellness, designed to help you grow and achieve your goals.', 'activity.avif', 'bi-lightning-charge'],
            ['events', 'Events', 'Exciting events for everyone', 'Join community events, workshops, and special activities that bring people together and inspire new experiences.', 'hero-bg.avif', 'bi-calendar-event']
        ] as [$key, $label, $title, $description, $image, $icon])
        <x-activity-card :category="$key" :label="$label" :title="$title" :description="$description" :image="$image" :icon="$icon" />
        @endforeach
    </div>
<p id="filter-status" class="sr-only" role="status" aria-live="polite" data-template="{{ __(':count categories shown') }}">
</p>
</section>
<section id="kids" class="bg-[#edf5f1] py-12 sm:py-[72px] dark:bg-[#182b28]">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px] grid grid-cols-1 items-center gap-10 sm:grid-cols-2 min-[1051px]:gap-20 [&_p]:mt-[22px] [&_p]:mb-7 [&_em]:block transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<div class="[&_img]:h-[280px] sm:[&_img]:h-[350px] [&_img]:w-full [&_img]:rounded-[16px_90px_16px_16px]">
<img src="{{ asset('images/swimming.webp') }}" alt="{{ __('Swimming and activities for children') }}" loading="lazy" width="680" height="500">
</div>
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('SMALL STEPS. BIG ADVENTURES.') }}</span>
<h2>{{ __('Big fun for') }} <em>{{ __('little movers.') }}</em>
</h2>
<p>{{ __('Fun activities for children From swimming lessons to sports clubs, our programs keep kids active and engaged') }}</p>
<x-button  href="#apps">{{ __('Activities For Kids') }} <span aria-hidden="true">↗</span>
</x-button>
</div>
</div>
</section>
@include('partials.apps')
<section id="about" class="py-[60px] sm:py-24 mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="mb-9 flex flex-col items-start justify-between gap-5 sm:flex-row sm:gap-9 min-[901px]:items-end [&>p]:text-[.9rem] sm:[&>p]:max-w-[280px] min-[901px]:[&>p]:max-w-[360px] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('MORE THAN A BOOKING') }}</span>
<h2>{{ __('Better, together.') }}</h2>
</div>
<p>{{ __('Our mission is to inspire people of all ages to live better, healthier, and more connected lives.') }}</p>
</div>@include('partials.values')<x-text-link class="mt-7" href="{{ route('about') }}">{{ __('More about Planoo') }} <span aria-hidden="true">↗</span>
</x-text-link>
</section>
@endsection
