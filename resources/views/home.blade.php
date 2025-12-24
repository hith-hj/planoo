@extends('layouts.main')
@section('content')
    {{-- hero --}}
    <section id="home" class="">
        <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
            <!-- Hero Text -->
            <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                {{__('Looking for something interesting?')}}
            </h1>
            <p class="text-white text-xl md:text-3xl mb-8 text-center drop-shadow-md">
                {{__('Let\'s help you find it')}}
            </p>

            <!-- Search Widget Container -->
            <div class="w-full max-w-4xl bg-transparent rounded-lg shadow-xl overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b">
                    <button class="flex-1 py-4 text-center font-bold text-lg tab-active "
                        onclick="showTab('court')"
                        id="tab-court">
                      {{__('Find court')}}
                    </button>
                    <button class="flex-1 py-4 text-center font-bold text-lg tab-inactive"
                        onclick="showTab('activity')"
                        id="tab-activity">
                      {{__('Find activity')}}
                    </button>
                </div>

                <!-- Search Body: Centre -->
                <div class="bg-white p-6 md:p-8 flex flex-col md:flex-row gap-4 items-center">
                    <div id="content-court" class="flex-1 w-full relative">
                      <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{__('Court')}}</label>
                      <input type="text" class="w-full bg-gray-100 border border-gray-300 rounded p-3 text-lg focus:outline-none focus:border-teal text-gray-700 font-medium">
                      <i class="bi bi-geo-alt text-xl absolute ltr:right-3 rtl:left-3 bottom-3"></i>
                    </div>

                    <div id="content-activity"  class="hidden flex-1 w-full relative">
                      <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{__('Activity')}}</label>
                      <input type="text" class="w-full bg-gray-100 border border-gray-300 rounded p-3 text-lg focus:outline-none focus:border-teal text-gray-700 font-medium">
                      <i class="bi bi-search text-xl absolute ltr:right-3 rtl:left-3 bottom-3"></i>
                    </div>

                    <button class="w-full md:w-auto bg-yellow hover:bg-yellow-400 text-purple font-bold text-lg py-3 px-10 rounded shadow-sm mt-5 md:mt-5 transition-colors">
                      {{__('Search')}}
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- sections --}}
    <section id="sections" class="py-16 bg-gray-100">
        <!-- Intro Text -->
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold text-purple mb-4">{{__('Welcom to Planoo')}}</h2>
            <p class="text-gray-600 leading-relaxed">
                {{ __('Welcom message') }}
            </p>
        </div>

        <!-- 3-Column Promo Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16 px-4 md:px-12">
            {{-- Courts --}}
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Courts') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-teal text-white px-4 py-2 font-bold text-sm">
                        {{ __('Courts') }}
                    </div>
                </div>
                <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                    {{ __('Wide range of courts') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Discover indoor and outdoor courts for basketball, tennis, and more.') }}
                </p>
                <a href="#" class="text-teal font-bold hover:underline flex items-center">
                    {{ __('View Courts') }}
                    <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                </a>
            </div>

            {{-- Activities --}}
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="{{ asset('images/swimming.webp') }}" alt="{{ __('Activities') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-purple text-white px-4 py-2 font-bold text-sm">
                        {{ __('Activities') }}
                    </div>
                </div>
                <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                    {{ __('Swimming & Lessons') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Enjoy recreational swimming, take lessons, or join a local swim club.') }}
                </p>
                <a href="#" class="text-teal font-bold hover:underline flex items-center">
                    {{ __('Find out more') }}
                    <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                </a>
            </div>

            {{-- Trainers --}}
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Trainers') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-yellow text-purple px-4 py-2 font-bold text-sm">
                        {{ __('Trainers') }}
                    </div>
                </div>
                <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                    {{ __('Expert trainers to guide you') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Choose from certified trainers and book classes to reach your fitness goals.') }}
                </p>
                <a href="#" class="text-teal font-bold hover:underline flex items-center">
                    {{ __('View Trainers') }}
                    <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                </a>
            </div>

            {{-- Courses --}}
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Courses') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-purple text-white px-4 py-2 font-bold text-sm">
                        {{ __('Courses') }}
                    </div>
                </div>
                <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                    {{ __('Learn new skills with expert guidance') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Explore a variety of courses from fitness to wellness, designed to help you grow and achieve your goals.') }}
                </p>
                <a href="#" class="text-teal font-bold hover:underline flex items-center">
                    {{ __('View Courses') }}
                    <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                </a>
            </div>

            {{-- Events --}}
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Events') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-teal text-purple px-4 py-2 font-bold text-sm">
                        {{ __('Events') }}
                    </div>
                </div>
                <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                    {{ __('Exciting events for everyone') }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('Join community events, workshops, and special activities that bring people together and inspire new experiences.') }}
                </p>
                <a href="#" class="text-teal font-bold hover:underline flex items-center">
                    {{ __('View Events') }}
                    <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- kids --}}
    <section id="kids" class="py-16">
        <div class="bg-white py-16">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-purple mb-4">{{__('Activities For Kids')}}</h2>
                <p>
                    {{__('Fun activities for children From swimming lessons to sports clubs, our programs keep kids active and engaged')}}
                </p>
            </div>

            <!-- 3-Column Promo Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-4 md:px-12">
                {{-- Courts --}}
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                        <img src="{{ asset('images/swimming.webp') }}" alt="{{ __('Courts') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-teal text-white px-4 py-2 font-bold text-sm">
                            {{ __('Courts') }}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                        {{ __('Wide range of courts') }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('Discover indoor and outdoor courts for basketball, tennis, and more.') }}
                    </p>
                    <a href="#" class="text-teal font-bold hover:underline flex items-center">
                        {{ __('View Courts') }}
                        <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                    </a>
                </div>

                {{-- Activities --}}
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                        <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Activities') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-purple text-white px-4 py-2 font-bold text-sm">
                            {{ __('Activities') }}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                        {{ __('Swimming & Lessons') }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('Enjoy recreational swimming, take lessons, or join a local swim club.') }}
                    </p>
                    <a href="#" class="text-teal font-bold hover:underline flex items-center">
                        {{ __('Find out more') }}
                        <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                    </a>
                </div>

                {{-- Trainers --}}
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                        <img src="{{ asset('images/swimming.webp') }}" alt="{{ __('Trainers') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute bottom-0 ltr:left-0 rtl:right-0 bg-yellow text-purple px-4 py-2 font-bold text-sm">
                            {{ __('Trainers') }}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-purple mb-2 group-hover:text-teal transition-colors">
                        {{ __('Expert trainers to guide you') }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('Choose from certified trainers and book classes to reach your fitness goals.') }}
                    </p>
                    <a href="#" class="text-teal font-bold hover:underline flex items-center">
                        {{ __('View Trainers') }}
                        <i class="bi bi-chevron-right rtl:rotate-180 text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Apps --}}
    <section id="apps" class="py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-purple">{{ __('Apps') }}</h2>
            <p class="text-gray-600 text-xl py-8">
                {{ __('Here you can find our applications') }}
            </p>

            <div  class="py-5">
                <h1 class="text-xl font-bold text-purple mb-4">{{__('Customers')}}</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                        <i class="bi bi-android2 text-7xl text-teal mb-4 mx-auto"></i>
                        <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>__('android')]) }}</h3>
                        <p class="text-gray-600">
                            {{ __('Download link',['type'=>__('android'),'user'=>__('customers')]) }}
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                        <i class="bi bi-apple text-7xl text-teal mb-4 mx-auto"></i>
                        <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>__('IOS')]) }}</h3>
                        <p class="text-gray-600">
                            {{ __('Download link',['type'=>__('IOS'),'user'=>__('customers')]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div  class="py-5">
                <h1 class="text-xl font-bold text-purple mb-4">{{__('Partners')}}</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                        <i class="bi bi-android2 text-7xl text-teal mb-4 mx-auto"></i>
                        <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>__('android')]) }}</h3>
                        <p class="text-gray-600">
                            {{ __('Download link',['type'=>__('android'),'user'=>__('partners')]) }}
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                        <i class="bi bi-apple text-7xl text-teal mb-4 mx-auto"></i>
                        <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>__('IOS')]) }}</h3>
                        <p class="text-gray-600">
                            {{ __('Download link',['type'=>__('IOS'),'user'=>__('partners')]) }}
                        </p>
                    </div>
                </div>
            </div>
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

    {{-- Why us --}}
    <section id="why-choose-us" class="py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto px-6 text-center py-20">
            <h2 class="text-3xl font-bold text-purple mb-4">{{ __('Why Choose Us') }}</h2>
            <p class="text-gray-600 mb-12">
                {{ __('We stand out by offering exceptional facilities, expert guidance, and a welcoming environment for everyone.') }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-16">
                <!-- Card 1 -->
                <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                    <i class="bi bi-building text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-lg font-bold text-purple mb-2">{{ __('Modern Facilities') }}</h3>
                    <p class="text-gray-600">
                        {{ __('State-of-the-art gyms, pools, and studios designed for your comfort.') }}
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                    <i class="bi bi-person-arms-up text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-lg font-bold text-purple mb-2">{{ __('Expert Trainers') }}</h3>
                    <p class="text-gray-600">
                        {{ __('Certified professionals to guide you every step of the way.') }}
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                    <i class="bi bi-circle text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-lg font-bold text-purple mb-2">{{ __('Inclusive Programs') }}</h3>
                    <p class="text-gray-600">
                        {{ __('Activities for all ages and abilities, from kids to seniors.') }}
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                    <i class="bi bi-balloon-heart text-7xl text-teal mb-4 mx-auto"></i>
                    <h3 class="text-lg font-bold text-purple mb-2">{{ __('Community Focus') }}</h3>
                    <p class="text-gray-600">
                        {{ __('We build connections through events, workshops, and social activities.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
