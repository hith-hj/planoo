<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Better | Leisure Centres, Gyms, Pools & Libraries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    {{-- colors: {
                        better: {
                            teal: '#00A099',       // The primary brand teal
                            tealhover: '#00857f',
                            purple: '#4C2263',     // The GLL/Better corporate purple
                            yellow: '#FFC845',     // CTA Yellow
                            dark: '#333333',       // Text color
                            lightgray: '#F4F4F4',  // Backgrounds
                            border: '#E5E5E5'
                        }
                    }, --}}
                    colors: {
                        better: {
                            teal: '#00A89C',       // The primary brand teal
                            tealhover: '#04cec8',
                            purple: '#462e8e',     // The GLL/Better corporate purple
                            yellow: '#FFC845',     // CTA Yellow
                            dark: '#333333',       // Text color
                            lightgray: '#F4F4F4',  // Backgrounds
                            border: '#E5E5E5'
                        }
                    },
                    {{-- colors: {
                        better:{
                            teal:'#00A89C',
                            tealhover:'#04cec8'
                            purple:'#462e8e',
                            yellow: '#FFC845',     // CTA Yellow
                            dark: '#333333',       // Text color
                            lightgray: '#F4F4F4',  // Backgrounds
                            border: '#E5E5E5'
                        }
                    }, --}}
                    fontFamily: {
                        // Better uses a clean sans stack similar to Verdana/Arial
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
        .hero-section {
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
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
    </style>
</head>
<body class="font-sans text-better-dark antialiased bg-white">

    <!-- TOP UTILITY BAR -->
    {{-- <div class="bg-better-lightgray text-xs py-2 hidden md:block border-b border-gray-200">
        <div class="max-w-container mx-auto px-4 flex justify-end space-x-6 text-gray-600">
            <a href="#" class="hover:text-better-teal">Help Centre</a>
            <a href="#" class="hover:text-better-teal">Corporate</a>
            <a href="#" class="hover:text-better-teal">Careers</a>
            <a href="#" class="hover:text-better-teal">GLL Sport Foundation</a>
        </div>
    </div> --}}

    <!-- MAIN HEADER -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-container mx-auto px-4 h-20 flex items-center justify-between">

            <!-- Logo -->
            <a href="#" class="flex items-center gap-2 group">
                <!-- Imitating the 'Better' text logo style -->
                <span class="text-4xl font-bold tracking-tight text-better-purple group-hover:opacity-90">
                    Planoo<span class="text-better-teal">.</span>
                </span>
                <span class="text-[10px] uppercase text-gray-500 font-semibold tracking-widest mt-2 hidden sm:block">
                    get better
                </span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-bold text-better-purple">
                <a href="#" class="hover:text-better-teal py-2">Leisure</a>
                <a href="#" class="hover:text-better-teal py-2">Libraries</a>
                <a href="#" class="hover:text-better-teal py-2">Health</a>
                <a href="#" class="hover:text-better-teal py-2">Spa Experience</a>
                <a href="#" class="hover:text-better-teal py-2">Lessons & Courses</a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Search Icon -->
                <button class="text-better-purple hover:text-better-teal p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                <!-- Login -->
                <a href="#" class="hidden sm:block text-sm font-bold text-better-purple hover:underline">
                    Log in
                </a>

                <!-- Join Button -->
                <a href="#" class="hidden sm:inline-block bg-better-teal hover:bg-better-tealhover text-white text-sm font-bold py-3 px-6 rounded-full transition-colors">
                    Join now
                </a>

                <!-- Mobile Menu Icon -->
                <button id="mobile-menu-btn" class="lg:hidden text-better-purple p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- HERO SECTION WITH SEARCH WIDGET -->
    <div class="relative w-full h-screen hero-section bg-blend-overlay bg-better-purple flex flex-col justify-center items-center">

        <!-- Hero Text -->
        <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
            Find your local centre
        </h1>

        <!-- Search Widget Container -->
        <div class="w-full max-w-4xl bg-transparent rounded-lg shadow-xl overflow-hidden">
            <!-- Tabs -->
            <div class="flex">
                <button class="flex-1 py-4 text-center font-bold text-lg tab-active">
                    Find a centre
                </button>
                <button class="flex-1 py-4 text-center font-bold text-lg tab-inactive">
                    Find an activity
                </button>
            </div>

            <!-- Search Body -->
            <div class="bg-white p-6 md:p-8 flex flex-col md:flex-row gap-4 items-center">
                <div class="flex-1 w-full relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location or Postcode</label>
                    <input type="text" placeholder="E.g. London or SE1" class="w-full bg-gray-100 border border-gray-300 rounded p-3 text-lg focus:outline-none focus:border-better-teal text-gray-700 font-medium">
                    <svg class="w-6 h-6 text-gray-400 absolute right-3 bottom-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>

                <button class="w-full md:w-auto bg-better-yellow hover:bg-yellow-400 text-better-purple font-bold text-lg py-3 px-10 rounded shadow-sm mt-5 md:mt-5 transition-colors">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="max-w-container mx-auto px-4 py-12">

        <!-- Intro Text -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-better-purple mb-4">Welcome to Better</h2>
            <p class="text-gray-600 leading-relaxed">
                We are a charitable social enterprise, meaning we don't have shareholders.
                Instead, we invest every penny back into our facilities, staff and customers.
            </p>
        </div>

        <!-- 3-Column Promo Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">

            <!-- Card 1: Courts -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
                        Courts
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Join Better today
                </h3>
                <p class="text-gray-600 mb-4">No contract options available. Access to over 200 centres.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    View Courts
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Card 2: Activities -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-purple text-white px-4 py-2 font-bold text-sm">
                        Activities
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Swimming & Lessons
                </h3>
                <p class="text-gray-600 mb-4">Book a swim, learn to swim or join a club.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    Find out more
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Card 3: App -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-yellow text-better-purple px-4 py-2 font-bold text-sm">
                        Digital
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Download our App
                </h3>
                <p class="text-gray-600 mb-4">Book classes on the go and manage your membership.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    Get the app
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-better-purple mb-4">Activities For Kids</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">

            <!-- Card 1: Courts -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
                        Courts
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Join Better today
                </h3>
                <p class="text-gray-600 mb-4">No contract options available. Access to over 200 centres.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    View Courts
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Card 2: Activities -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-purple text-white px-4 py-2 font-bold text-sm">
                        Activities
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Swimming & Lessons
                </h3>
                <p class="text-gray-600 mb-4">Book a swim, learn to swim or join a club.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    Find out more
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Card 3: App -->
            <div class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-better-yellow text-better-purple px-4 py-2 font-bold text-sm">
                        Digital
                    </div>
                </div>
                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
                    Download our App
                </h3>
                <p class="text-gray-600 mb-4">Book classes on the go and manage your membership.</p>
                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
                    Get the app
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Grey Banner Section -->
        <div class="bg-better-lightgray rounded-xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="md:w-2/3">
                <h2 class="text-2xl font-bold text-better-purple mb-3">Not sure where to start?</h2>
                <p class="text-gray-600">Discover the variety of activities we offer across the UK, from Trampoline parks to Thermal spas.</p>
            </div>
            <div>
                <a href="#" class="inline-block bg-white border-2 border-better-purple text-better-purple hover:bg-better-purple hover:text-white font-bold py-3 px-8 rounded-full transition-colors">
                    Explore Activities
                </a>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="bg-better-purple text-white pt-16 pb-8">
        <div class="max-w-container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">

                <!-- Col 1 -->
                <div>
                    <h4 class="font-bold text-lg mb-6 border-b border-white/20 pb-2">About Us</h4>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-white">Our Story</a></li>
                        <li><a href="#" class="hover:text-white">Social Enterprise</a></li>
                        <li><a href="#" class="hover:text-white">Careers</a></li>
                        <li><a href="#" class="hover:text-white">News</a></li>
                    </ul>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="font-bold text-lg mb-6 border-b border-white/20 pb-2">Legal</h4>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-white">Terms & Conditions</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Cookies</a></li>
                        <li><a href="#" class="hover:text-white">Accessibility</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="font-bold text-lg mb-6 border-b border-white/20 pb-2">Help</h4>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Give Feedback</a></li>
                        <li><a href="#" class="hover:text-white">Safeguarding</a></li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h4 class="font-bold text-lg mb-6 border-b border-white/20 pb-2">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-better-teal transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-better-teal transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-better-teal transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"></rect><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" stroke-width="2"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2"></line></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-400">
                <p>&copy; 2024 GLL. All rights reserved. Registered Charity No: 1122858.</p>
                <div class="mt-4 md:mt-0">
                    <span class="opacity-50">Part of the GLL group</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Container (Hidden by default) -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 hidden z-40"></div>
    <div id="mobile-menu" class="fixed top-0 right-0 h-full w-64 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300">
        <div class="p-4 flex justify-end">
            <button id="close-menu-btn" class="text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4 font-bold text-better-purple">
            <a href="#" class="block py-2 border-b">Leisure</a>
            <a href="#" class="block py-2 border-b">Libraries</a>
            <a href="#" class="block py-2 border-b">Health</a>
            <a href="#" class="block py-2 border-b">Spa Experience</a>
            <a href="#" class="block py-2 border-b">Log in</a>
            <a href="#" class="block py-3 mt-4 text-center bg-better-teal text-white rounded">Join Now</a>
        </div>
    </div>

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
</body>
</html>
