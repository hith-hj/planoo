@extends('layouts.main')
@section('content')
    {{-- hero --}}
    <section id="home" class="">
        <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
            <!-- Hero Text -->
            <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                {{ __('About Us') }}
            </h1>
            <p class="text-white text-xl md:text-3xl mb-8 text-center drop-shadow-md w-full md:w-1/2">
                {{ __('We are dedicated to creating vibrant spaces where health, learning, and community come together.') }}
            </p>

        </div>
    </section>

    {{-- About --}}
    <section id="about" class="py-16">
        <div class="max-w-6xl mx-auto px-6 text-center py-20">
            <h2 class="text-3xl font-bold text-purple mb-4">{{ __('About Us') }}</h2>
            <p class="text-gray-600 mb-12">
                {{ __('We are dedicated to creating vibrant spaces where health, learning, and community come together.') }}
                <br>
                {{__('Our mission is to inspire people of all ages to live better, healthier, and more connected lives.')}}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <!-- Card 1 -->
                <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                    <i class="bi bi-eye text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our Vision') }}</h3>
                    <p class="text-gray-600">{{ __('To empower communities by promoting wellness, education, and social connection.') }}</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                    <i class="bi bi-check text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our Mission') }}</h3>
                    <p class="text-gray-600">{{ __('To provide accessible facilities and programs that inspire healthier lifestyles.') }}</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                    <i class="bi bi-plus text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our Values') }}</h3>
                    <p class="text-gray-600">{{ __('Integrity, inclusivity, and innovation guide everything we do.') }}</p>
                </div>
            </div>
        </div>
    </section>

@endsection
