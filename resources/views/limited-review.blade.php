@extends('layouts.main')
@section('title', __('Review Link'))
@section('content')
<section class="bg-[#f3f0f9] py-[52px] text-center sm:py-[76px] dark:bg-[#251e35] [&_h1]:text-[clamp(2.5rem,5vw,4rem)] [&_h1]:text-accent [&_p]:mx-auto [&_p]:mt-[22px] [&_p]:max-w-[650px]">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">PLANOO</span>
<h1>{{ __('Review Link') }}</h1>
<p>{{ __('Location Usage') }}</p>
</div>
</section>
<section class="pt-12 pb-20 mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="mx-auto max-w-[430px] overflow-hidden rounded-[20px] bg-[#191622] [&_video]:block [&_video]:max-h-[75vh] [&_video]:w-full">
<video controls playsinline preload="metadata" aria-label="{{ __('Location Usage') }}">
<source src="{{ asset('uploads/video.mp4') }}" type="video/mp4">{{ __('Your browser does not support the video tag.') }} <a href="{{ asset('uploads/video.mp4') }}">{{ __('Download video') }}</a>
</video>
</div>
</section>
@endsection
