<!DOCTYPE html>
<html lang="en" dir="{{app()->getLocale() === 'ar'? 'rtl':'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Better | Leisure Centres, Gyms, Pools & Libraries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: '#00A89C',
                        tealhover: '#04cec8',
                        purple: '#462e8e',
                        yellow: '#FFC845',
                        dark: '#333333',
                        lightgray: '#F4F4F4',
                        border: '#E5E5E5'
                    },
                    fontFamily: {
                        sans: ['Verdana', 'Arial', 'sans-serif'],
                    },
                    maxWidth: {
                        'container': '1180px'
                    }
                }
            }
        }
    </script>
    <style>
        /* Specific overrides to match the exact "Better" feel */
        .hero-bg {
            background-image: url('{{asset('images/hero-bg.avif')}}');
            background-size: cover;
            background-position: center;
        }

        .tab-active {
            background-color: white;
            color: #4C2263;
            border-top: 4px solid #00A099;
        }

        .tab-inactive {
            background-color: rgba(255,255,255,0.9);
            color: #666;
            border-top: 4px solid transparent;
        }
        .tab-inactive:hover {
            background-color: #fff;
        }
        /* Hide scrollbar for Chrome, Safari, and Opera */
        .scrollbar-hide::-webkit-scrollbar {
          display: none;
        }

        /* Hide scrollbar for IE, Edge, and Firefox */
        .scrollbar-hide {
          -ms-overflow-style: none;  /* IE and Edge */
          scrollbar-width: none;     /* Firefox */
        }
    </style>
</head>
<body class="font-sans text-dark antialiased bg-white rtl:text-2xl">
    <!-- MAIN HEADER -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-container mx-auto px-4 h-20 flex items-center justify-between">

            <!-- Logo -->
            <a href="#" class="flex items-center gap-2 group">
                <span class="text-4xl font-bold tracking-tight text-purple group-hover:opacity-90">
                    Planoo<span class="text-teal">.</span>
                </span>
                <span class="text-md uppercase text-gray-500 font-semibold tracking-widest mt-2 hidden sm:block">
                    {{ __('get better') }}
                </span>
            </a>
            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-8 font-bold text-purple">
                <a href="#home" data-section="home" class="hover:text-teal py-2">{{ __('Home') }}</a>
                <a href="#sections" data-section="sections" class="hover:text-teal py-2">{{ __('Sections') }}</a>
                <a href="#apps" data-section="apps" class="hover:text-teal py-2">{{ __('Apps') }}</a>
                <a href="#about" data-section="about" class="hover:text-teal py-2">{{ __('About us') }}</a>
                <a href="#contact" data-section="contact" class="hover:text-teal py-2">{{ __('Contact') }}</a>
                <div class="relative py-2">
                    <!-- Dropdown Button -->
                    <button type="button"
                            class="inline-flex justify-center w-full hover:text-teal focus:outline-none"
                            onclick="document.getElementById('langMenu').classList.toggle('hidden')">
                        <i class="bi bi-globe text-xl"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="langMenu"
                         class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-center">
                        <div class="py-1">
                            <a href="{{ route('lang',['locale'=>'ar']) }}"
                               class="block px-4 py-2 hover:text-teal {{ app()->getLocale() == 'ar' ? 'text-teal' : '' }}">
                                {{ __('Arabic') }}
                            </a>
                            <a href="{{ route('lang',['locale'=>'en']) }}"
                               class="block px-4 py-2 hover:text-teal {{ app()->getLocale() == 'en' ? 'text-teal': '' }}">
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Login -->
                <a href="#" class="hidden sm:block font-bold text-purple hover:underline">
                    {{ __('Log in') }}
                </a>

                <!-- Join Button -->
                <a href="#" class="hidden sm:inline-block bg-teal hover:bg-tealhover text-white font-bold py-3 px-6 rounded-full transition-colors">
                    {{ __('Join now') }}
                </a>

                <!-- Mobile Menu Icon -->
                <button id="mobile-menu-btn" class="lg:hidden text-purple p-2">
                    <i class="bi bi-list text-3xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 hidden z-40"></div>
        <div id="mobile-menu"
             class="fixed top-0 right-0 h-full w-64 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300">
            <div class="p-4 flex justify-end">
                <button id="close-menu-btn" class="text-gray-500">
                    <i class="bi bi-x text-4xl"></i>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4 font-bold text-purple">
                <a href="#home" data-section="home" class="block py-2 border-b  hover:text-teal py-2">
                    {{ __('Home') }}
                </a>
                <a href="#sections" data-section="sections" class="block py-2 border-b  hover:text-teal py-2">
                    {{ __('Sections') }}
                </a>
                <a href="#apps" data-section="apps" class="block py-2 border-b  hover:text-teal py-2">
                    {{ __('Apps') }}
                </a>
                <a href="#about" data-section="about" class="block py-2 border-b  hover:text-teal py-2">
                    {{ __('About us') }}
                </a>
                <a href="#contact" data-section="contact" class="block py-2 border-b  hover:text-teal py-2">
                    {{ __('Contact') }}
                </a>

                <a href="#" class="block py-2 border-b hover:text-teal py-2">{{ __('Log in') }}</a>
                <div class="relative py-2">
                    <!-- Dropdown Button -->
                    <button type="button"
                            class="block hover:text-teal focus:outline-none"
                            onclick="document.getElementById('mobileLangMenu').classList.toggle('hidden')">
                        <i class="bi bi-globe text-xl"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="mobileLangMenu"
                         class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-center">
                        <div class="py-1">
                            <a href="{{ route('lang',['locale'=>'ar']) }}"
                               class="block px-4 py-2 hover:text-teal {{ app()->getLocale() == 'ar' ? 'text-teal' : '' }}">
                                {{ __('Arabic') }}
                            </a>
                            <a href="{{ route('lang',['locale'=>'en']) }}"
                               class="block px-4 py-2 hover:text-teal {{ app()->getLocale() == 'en' ? 'text-teal': '' }}">
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>
                </div>
                <a href="#" class="block py-3 mt-4 text-center bg-teal text-white rounded">{{ __('Join now') }}</a>

            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main classx="max-w-container">

        <!-- HERO SECTION WITH SEARCH WIDGET -->
        <section id="home" class="pb-10">
            <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
                <!-- Hero Text -->
                <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                    {{__('Find something to do')}}
                </h1>

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

        <section id="sections" class="h-full mx-auto bg-gray-100">
            <!-- Intro Text -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-purple mb-4">{{__('Welcom to Planoo')}}</h2>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('Welcom message') }}
                </p>
            </div>

            <!-- 3-Column Promo Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16 p-12">
                {{-- Courts --}}
                <div class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                        <img src="{{ asset('images/activity.avif') }}" alt="{{ __('Courts') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute bottom-0 left-0 bg-teal text-white px-4 py-2 font-bold text-sm">
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
                        <div class="absolute bottom-0 left-0 bg-purple text-white px-4 py-2 font-bold text-sm">
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
                        <div class="absolute bottom-0 left-0 bg-yellow text-purple px-4 py-2 font-bold text-sm">
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
                        <div class="absolute bottom-0 left-0 bg-purple text-white px-4 py-2 font-bold text-sm">
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
                        <div class="absolute bottom-0 left-0 bg-teal text-purple px-4 py-2 font-bold text-sm">
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

            <div class=" bg-white py-16">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-purple mb-4">{{__('Activities For Kids')}}</h2>
                    <p>
                        {{__('Fun activities for children From swimming lessons to sports clubs, our programs keep kids active and engaged')}}
                    </p>
                </div>

                <!-- 3-Column Promo Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16 p-12">
                    {{-- Courts --}}
                    <div class="group cursor-pointer">
                        <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                            <img src="{{ asset('images/swimming.webp') }}" alt="{{ __('Courts') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute bottom-0 left-0 bg-teal text-white px-4 py-2 font-bold text-sm">
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
                            <div class="absolute bottom-0 left-0 bg-purple text-white px-4 py-2 font-bold text-sm">
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
                            <div class="absolute bottom-0 left-0 bg-yellow text-purple px-4 py-2 font-bold text-sm">
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
                <h2 class="text-3xl font-bold text-purple mb-4">{{ __('Apps') }}</h2>
                <p class="text-gray-600 mb-12">
                    {{ __('Here you can find our applications') }}
                </p>

                <div  class="py-10">
                    <h1 class="text-xl font-bold text-purple mb-4">{{__('Customers')}}</h1>
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Card 1 -->
                        <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                            <i class="bi bi-android2 text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>'Android']) }}</h3>
                            <p class="text-gray-600">
                                {{ __('Download link',['type'=>'android','user'=>'customers']) }}
                            </p>
                        </div>

                        <!-- Card 2 -->
                        <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                            <i class="bi bi-apple text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>'IOS']) }}</h3>
                            <p class="text-gray-600">
                                {{ __('Download link',['type'=>'IOS','user'=>'customers']) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div  class="py-10">
                    <h1 class="text-xl font-bold text-purple mb-4">{{__('Clients')}}</h1>
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Card 1 -->
                        <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                            <i class="bi bi-android2 text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>'Android']) }}</h3>
                            <p class="text-gray-600">
                                {{ __('Download link',['type'=>'android','user'=>'client']) }}
                            </p>
                        </div>

                        <!-- Card 2 -->
                        <div class="bg-white rounded-lg shadow-lg p-6 group hover:shadow-xl transition">
                            <i class="bi bi-apple text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-xl font-bold text-purple mb-2">{{ __('Our App',['type'=>'IOS']) }}</h3>
                            <p class="text-gray-600">
                                {{ __('Download link',['type'=>'IOS','user'=>'client']) }}
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
                    {{ __('We are dedicated to creating vibrant spaces where health, learning, and community come together. Our mission is to inspire people of all ages to live better, healthier, and more connected lives.') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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

            <section id="why-choose-us" class="py-16 bg-gray-100">
                <div class="max-w-6xl mx-auto px-6 text-center">
                    <h2 class="text-3xl font-bold text-purple mb-4">{{ __('Why Choose Us') }}</h2>
                    <p class="text-gray-600 mb-12">
                        {{ __('We stand out by offering exceptional facilities, expert guidance, and a welcoming environment for everyone.') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Card 1 -->
                        <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                            <i class="bi bi-building text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-lg font-bold text-purple mb-2">{{ __('Modern Facilities') }}</h3>
                            <p class="text-gray-600">{{ __('State-of-the-art gyms, pools, and studios designed for your comfort.') }}</p>
                        </div>

                        <!-- Card 2 -->
                        <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                            <i class="bi bi-person-arms-up text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-lg font-bold text-purple mb-2">{{ __('Expert Trainers') }}</h3>
                            <p class="text-gray-600">{{ __('Certified professionals to guide you every step of the way.') }}</p>
                        </div>

                        <!-- Card 3 -->
                        <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                            <i class="bi bi-circle text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-lg font-bold text-purple mb-2">{{ __('Inclusive Programs') }}</h3>
                            <p class="text-gray-600">{{ __('Activities for all ages and abilities, from kids to seniors.') }}</p>
                        </div>

                        <!-- Card 4 -->
                        <div class="bg-gray-50 rounded-lg shadow p-6 hover:bg-teal/10 transition">
                            <i class="bi bi-balloon-heart text-7xl text-teal mb-4 mx-auto"></i>
                            <h3 class="text-lg font-bold text-purple mb-2">{{ __('Community Focus') }}</h3>
                            <p class="text-gray-600">{{ __('We build connections through events, workshops, and social activities.') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </section>

        {{-- <section id="contact" class="">
            <footer id="contact" class="hero-bg h-fit md:h-full bg-blend-multiply bg-purple text-white">
                <div class="flex justify-center items-start">
                    <h1 class="text-6xl md:text-9xl font-extrabold text-white uppercase
                    [text-shadow:_0_10px_15px_rgba(0,0,0,0.3)]">
                        questions?
                    </h1>
                </div>
                <div class="container mx-auto px-5 md:px-20 py-12">
                    <div class="w-full flex flex-col md:flex-row gap-2 justify-between bg-white/40 text-white p-10 my-5">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-semibold capitalize shadow-2xl">
                                Any question ?
                            </h1>
                            <p>If you have any qustion dont hasitate, you can ask any thing you want</p>
                        </div>
                        <div class="flex justify-end">
                            <button class="w-full md:w-auto bg-yellow text-purple font-medium py-4 px-8 rounded-lg transition duration-300 flex items-center justify-center space-x-2 shadow-md">
                                <span>Contact</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-8 my-5">
                        <div >
                            <div class="flex items-center gap-2 mb-4">
                                <div class="group flex flex-col items-start justify-center">
                                    <span class="text-4xl font-bold tracking-tight text-teal group-hover:opacity-90">
                                        Planoo<span class="text-teal">.</span>
                                    </span>
                                    <span class="uppercase text-gray-500 font-semibold tracking-widest hidden sm:block">
                                        {{__('get better')}}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-100 text-sm max-w-sm mb-6 leading-relaxed">
                                We help individuals and businesses find their dream properties in urban and suburban areas worldwide.
                            </p>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">Company</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">About</a></li>
                                <li><a href="#" class="hover:text-gold transition">Blog</a></li>
                                <li><a href="#" class="hover:text-gold transition">All products</a></li>
                                <li><a href="#" class="hover:text-gold transition">Locations map</a></li>
                                <li><a href="#" class="hover:text-gold transition">FAQ</a></li>
                                <li><a href="#" class="hover:text-gold transition">Contact us</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">Services</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">Order Tracking</a></li>
                                <li><a href="#" class="hover:text-gold transition">Wish list</a></li>
                                <li><a href="#" class="hover:text-gold transition">Login</a></li>
                                <li><a href="#" class="hover:text-gold transition">My account</a></li>
                                <li><a href="#" class="hover:text-gold transition">Terms & Condition</a></li>
                                <li><a href="#" class="hover:text-gold transition">Offers</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">Customers core</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">Buy a Property</a></li>
                                <li><a href="#" class="hover:text-gold transition">Rent a Property</a></li>
                                <li><a href="#" class="hover:text-gold transition">Sell a Property</a></li>
                                <li><a href="#" class="hover:text-gold transition">Mortgage Service</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-2 justify-between items-center pb-1">
                      <!-- Left Section -->
                      <div class="flex flex-1 justify-between text-white">
                        <a href="#" class="flex items-end hover:text-gold transition">
                          <i class="bi bi-geo-alt px-2 text-3xl"></i>
                          <span>New York</span>
                        </a>
                        <a href="tel:+123456789" class="flex items-end hover:text-gold transition">
                          <i class="bi bi-telephone px-2 text-3xl"></i>
                          <span>Phone*</span>
                        </a>
                        <a href="mailto:info@darion.com" class="flex items-end hover:text-gold transition">
                          <i class="bi bi-envelope px-2 text-3xl"></i>
                          <span>Email*</span>
                        </a>
                      </div>

                      <div class="flex flex-1"></div>

                      <!-- Right Section -->
                      <div class="flex flex-1 gap-3 justify-end">
                        <h1 class="text-xl font-semibold">Follow us</h1>
                        <div class="flex space-x-4">
                          <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                            <i class="bi bi-instagram"></i>
                          </a>
                          <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                            <i class="bi bi-facebook"></i>
                          </a>
                          <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                            <i class="bi bi-twitter-x"></i>
                          </a>
                        </div>
                      </div>
                    </div>

                </div>
            </footer>
        </section> --}}
        <section id="contact" class="">
            <footer id="contact" class="hero-bg h-fit md:h-full bg-blend-multiply bg-purple text-white">
                <div class="flex justify-center items-start">
                    <h1 class="text-6xl md:text-9xl font-extrabold text-white uppercase
                    [text-shadow:_0_10px_15px_rgba(0,0,0,0.3)]">
                        {{ __('Questions?') }}
                    </h1>
                </div>
                <div class="container mx-auto px-5 md:px-20 py-12">
                    <div class="w-full flex flex-col md:flex-row gap-2 justify-between bg-white/40 text-white p-10 my-5">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-semibold capitalize shadow-2xl">
                                {{ __('Any question?') }}
                            </h1>
                            <p>{{ __('If you have any question don’t hesitate, you can ask anything you want.') }}</p>
                        </div>
                        <div class="flex justify-end">
                            <button class="w-full md:w-auto bg-yellow text-purple font-medium py-4 px-8 rounded-lg transition duration-300 flex items-center justify-center space-x-2 shadow-md">
                                <span>{{ __('Contact') }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-8 my-5">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="group flex flex-col items-start justify-center">
                                    <span class="text-4xl font-bold tracking-tight text-teal group-hover:opacity-90">
                                        Planoo<span class="text-teal">.</span>
                                    </span>
                                    <span class="uppercase text-gray-500 font-semibold tracking-widest hidden sm:block">
                                        {{ __('get better') }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-100 text-sm max-w-sm mb-6 leading-relaxed">
                                {{ __('We help individuals and businesses find their dream properties in urban and suburban areas worldwide.') }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">{{ __('Company') }}</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">{{ __('About') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('Locations map') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('FAQ') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('Contact us') }}</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">{{ __('Services') }}</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">{{ __('Login') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('My account') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('Terms & Condition') }}</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-white mb-6">{{ __('Customers core') }}</h4>
                            <ul class="space-y-3 text-gray-100 text-sm">
                                <li><a href="#" class="hover:text-gold transition">{{ __('Blog') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('All products') }}</a></li>
                                <li><a href="#" class="hover:text-gold transition">{{ __('Offers') }}</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-2 justify-between items-center pb-1">
                        <!-- Left Section -->
                        <div class="flex flex-1 justify-between text-white">
                            <a href="#" class="flex items-end hover:text-gold transition">
                                <i class="bi bi-geo-alt px-2 text-3xl"></i>
                                <span>{{ __('New York') }}</span>
                            </a>
                            <a href="tel:+123456789" class="flex items-end hover:text-gold transition">
                                <i class="bi bi-telephone px-2 text-3xl"></i>
                                <span>{{ __('Phone') }}</span>
                            </a>
                            <a href="mailto:info@darion.com" class="flex items-end hover:text-gold transition">
                                <i class="bi bi-envelope px-2 text-3xl"></i>
                                <span>{{ __('Email') }}</span>
                            </a>
                        </div>

                        <div class="flex flex-1"></div>

                        <!-- Right Section -->
                        <div class="flex flex-1 gap-3 justify-end">
                            <h1 class="text-xl font-semibold">{{ __('Follow us') }}</h1>
                            <div class="flex gap-4">
                                <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full text-black bg-white hover:bg-gold transition">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </section>

    </main>

    {{-- script for responsive menu --}}
    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-menu-overlay');

        function toggleMenu() {
            const isClosed = menu.classList.contains('translate-x-full');
            if (isClosed) {
                menu.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                menu.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        menuBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
    </script>

    {{-- script for hero search section --}}
    <script>
      function showTab(tab) {
        // Hide both contents
        document.getElementById('content-court').classList.add('hidden');
        document.getElementById('content-activity').classList.add('hidden');

        // Remove active styles
        document.getElementById('tab-court').classList.remove('tab-active','border-teal');
        document.getElementById('tab-activity').classList.remove('tab-active','border-teal');

        // Add inactive styles
        document.getElementById('tab-court').classList.add('tab-inactive');
        document.getElementById('tab-activity').classList.add('tab-inactive');

        // Show selected content + activate tab
        if(tab === 'court') {
          document.getElementById('content-court').classList.remove('hidden');
          document.getElementById('tab-court').classList.add('tab-active','border-teal');
          document.getElementById('tab-court').classList.remove('tab-inactive');
        } else {
          document.getElementById('content-activity').classList.remove('hidden');
          document.getElementById('tab-activity').classList.add('tab-active','border-teal');
          document.getElementById('tab-activity').classList.remove('tab-inactive');
        }
      }
    </script>

    {{-- script for nav taps  --}}
    <script>
      const observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    document.querySelectorAll('a[data-section]').forEach(link =>link.classList.remove('text-teal') );
                    const activeLink = document.querySelector(`a[data-section="${entry.target.id}"]`);
                    if (activeLink) {
                        activeLink.classList.add('text-teal');
                    }
                }
            });
        },
        { threshold: 0.6 }
      );
      document.querySelectorAll('section').forEach(section => observer.observe(section));
    </script>

</body>
</html>
