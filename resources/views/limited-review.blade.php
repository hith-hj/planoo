@extends('layouts.main')
@section('content')
    {{-- hero --}}
    <section id="home" class="">

        <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
            <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                Review Link
            </h1>
        </div>
    </section>

	<div class="bg-zinc-950 flex items-center justify-center min-h-screen p-1 ">

	    <div class="relative w-full max-w-[450px] aspect-[9/16] bg-black rounded-xl overflow-hidden shadow-xl ring-1 ring-white/30
	    group pt-4">

	        <video
	            class="w-full h-full object-contain"
	            controls
	            playsinline
	        >
	            <source src="{{ asset('uploads/video.mp4') }}" type="video/mp4">
	            Your browser does not support the video tag.
	        </video>

	        <div class="absolute inset-0 pointer-events-none bg-gradient-to-b from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
	            <div class="absolute top-12 left-6 text-white">
	                <h3 class="font-semibold text-lg">Location Usage</h3>
	                <p class="text-sm opacity-80">Review Link</p>
	            </div>
	        </div>

	    </div>
	</div>

@endsection
