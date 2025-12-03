<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Better - Template Clone</title>
    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configure Tailwind to match the site's colors and use Inter font -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'better-blue': '#0054A5', // Primary blue
                        'better-dark': '#003366', // Deep nav/footer blue
                        'better-teal': '#00A89C', // Accent teal
                        'better-gray': '#E5E7EB', // Light background gray
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom styles for better visual fidelity */
        .hero-bg {
            background-image: url('https://placehold.co/1920x800/003366/ffffff/png?text=Placeholder+Image+%26+Overlay');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white">

    <!-- Header & Navigation -->
    <header class="sticky top-0 z-50 shadow-md bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="text-3xl font-extrabold text-better-dark rounded-lg p-2 transition hover:text-better-teal">
                    Planoo.
                </a>

                <!-- Desktop Navigation (Hidden on Mobile) -->
                <nav class="hidden lg:flex space-x-6 items-center">
                    <a href="#" class="text-better-dark hover:text-better-teal font-medium transition py-2 px-3 rounded-lg">What's On</a>
                    <a href="#" class="text-better-dark hover:text-better-teal font-medium transition py-2 px-3 rounded-lg">Locations</a>
                    <a href="#" class="text-better-dark hover:text-better-teal font-medium transition py-2 px-3 rounded-lg">Memberships</a>
                    <a href="#" class="text-better-dark hover:text-better-teal font-medium transition py-2 px-3 rounded-lg">Activities</a>
                    <a href="#" class="text-better-dark hover:text-better-teal font-medium transition py-2 px-3 rounded-lg">About Us</a>
                </nav>

                <!-- Secondary Actions & Mobile Menu Button -->
                <div class="flex items-center space-x-4">
                    <button class="text-better-dark hover:text-better-teal p-2 rounded-full transition hidden lg:block" aria-label="Search">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <a href="#" class="hidden sm:block text-better-dark hover:text-better-teal font-semibold px-4 py-2 rounded-lg transition">Log in</a>
                    <a href="#" class="bg-better-teal hover:bg-better-blue text-white font-bold px-4 py-2 rounded-xl shadow-lg transition duration-300 transform hover:scale-105">Join Now</a>

                    <!-- Mobile Menu Button (Visible on Mobile) -->
                    <button id="mobile-menu-button" class="lg:hidden p-2 text-better-dark rounded-md hover:bg-better-gray" aria-label="Open menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown (Initially Hidden) -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white shadow-xl absolute w-full pb-4 border-t border-better-gray">
            <div class="px-4 pt-2 space-y-2">
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium">What's On</a>
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium">Locations</a>
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium">Memberships</a>
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium">Activities</a>
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium">About Us</a>
                <a href="#" class="block text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium border-t pt-2 mt-2">Log in</a>
                <button class="w-full text-better-dark hover:bg-better-gray py-2 px-3 rounded-lg font-medium text-left">Search</button>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-bg h-screen flex items-center justify-center text-center">
            <div class="bg-black bg-opacity-40 p-8 md:p-16 rounded-3xl mx-4 max-w-4xl shadow-2xl">
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white leading-tight mb-4">
                    Get Active. Live Better.
                </h1>
                <p class="text-xl text-gray-200 mb-8">
                    Discover your local gym, pool, or leisure centre and start your fitness journey today.
                </p>
                <a href="#" class="inline-block bg-better-teal hover:bg-better-dark text-white text-lg font-bold py-4 px-10 rounded-full shadow-xl transition duration-300 transform hover:scale-105">
                    Find a Centre
                </a>
            </div>
        </section>

        <!-- Key Services Grid -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-better-dark mb-12">Popular Activities</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="group cursor-pointer">
		                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
		                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
		                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
		                        Swimming
		                    </div>
		                </div>
		                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
		                    Lanes, lessons, and leisure. Find pool timetables near you
		                </h3>
		                <p class="text-gray-600 mb-4">Access to over 200 centres.</p>
		                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
		                    View Swimming
		                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
		                </a>
		            </div>
		            <div class="group cursor-pointer">
		                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
		                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
		                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
		                        Swimming
		                    </div>
		                </div>
		                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
		                    Lanes, lessons, and leisure. Find pool timetables near you
		                </h3>
		                <p class="text-gray-600 mb-4">Access to over 200 centres.</p>
		                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
		                    View Swimming
		                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
		                </a>
		            </div>
		            <div class="group cursor-pointer">
		                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
		                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
		                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
		                        Swimming
		                    </div>
		                </div>
		                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
		                    Lanes, lessons, and leisure. Find pool timetables near you
		                </h3>
		                <p class="text-gray-600 mb-4">Access to over 200 centres.</p>
		                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
		                    View Swimming
		                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
		                </a>
		            </div>
		            <div class="group cursor-pointer">
		                <div class="relative overflow-hidden rounded-lg mb-4 h-64">
		                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Gym" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
		                    <div class="absolute bottom-0 left-0 bg-better-teal text-white px-4 py-2 font-bold text-sm">
		                        Swimming
		                    </div>
		                </div>
		                <h3 class="text-xl font-bold text-better-purple mb-2 group-hover:text-better-teal transition-colors">
		                    Lanes, lessons, and leisure. Find pool timetables near you
		                </h3>
		                <p class="text-gray-600 mb-4">Access to over 200 centres.</p>
		                <a href="#" class="text-better-teal font-bold hover:underline flex items-center">
		                    View Swimming
		                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
		                </a>
		            </div>
                </div>
            </div>
        </section>

        <!-- Activities For kids -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-better-dark mb-12">Activities For kids</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-better-gray p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <h3 class="text-xl font-bold text-better-dark mb-2">Swimming</h3>
                        <p class="text-gray-600">Lanes, lessons, and leisure. Find pool timetables near you.</p>
                        <a href="#" class="text-better-blue font-semibold mt-3 block hover:text-better-teal">View Options &rarr;</a>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-better-gray p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <h3 class="text-xl font-bold text-better-dark mb-2">Gym & Fitness</h3>
                        <p class="text-gray-600">State-of-the-art equipment and functional training zones.</p>
                        <a href="#" class="text-better-blue font-semibold mt-3 block hover:text-better-teal">View Options &rarr;</a>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-better-gray p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <h3 class="text-xl font-bold text-better-dark mb-2">Classes</h3>
                        <p class="text-gray-600">From Yoga to HIIT, find a class to suit your pace.</p>
                        <a href="#" class="text-better-blue font-semibold mt-3 block hover:text-better-teal">View Options &rarr;</a>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-better-gray p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                        <h3 class="text-xl font-bold text-better-dark mb-2">Sports & Racquets</h3>
                        <p class="text-gray-600">Book courts for tennis, badminton, squash, and more.</p>
                        <a href="#" class="text-better-blue font-semibold mt-3 block hover:text-better-teal">View Options &rarr;</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Secondary CTA Banner -->
        <section class="py-16 bg-better-blue">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:flex md:justify-between md:items-center">
                <h2 class="text-3xl font-bold text-white mb-6 md:mb-0 md:text-left">
                    Ready to join the network?
                </h2>
                <a href="#" class="inline-block bg-white text-better-blue hover:bg-better-teal hover:text-white text-lg font-bold py-3 px-8 rounded-full shadow-xl transition duration-300 transform hover:scale-105">
                    Explore Here
                </a>
            </div>
        </section>

        <!-- Latest News/Blog Teaser -->
        <section class="py-16 bg-better-gray">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-better-dark mb-12">Latest News & Updates</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Article 1 -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                        <img src="https://placehold.co/400x200/00A89C/ffffff/png?text=Fitness+Update" alt="News Image" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="text-sm font-semibold text-better-teal uppercase">Fitness</span>
                            <h3 class="text-xl font-bold text-better-dark mt-1 mb-3">Our New Class Timetables</h3>
                            <p class="text-gray-600">We've added over 50 new classes across all locations. Book now!</p>
                        </div>
                    </div>
                    <!-- Article 2 -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                        <img src="https://placehold.co/400x200/0054A5/ffffff/png?text=Community+Event" alt="News Image" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="text-sm font-semibold text-better-teal uppercase">Community</span>
                            <h3 class="text-xl font-bold text-better-dark mt-1 mb-3">Supporting Local Schools</h3>
                            <p class="text-gray-600">Read about our latest community outreach program in the North East.</p>
                        </div>
                    </div>
                    <!-- Article 3 -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                        <img src="https://placehold.co/400x200/003366/ffffff/png?text=Pool+Safety" alt="News Image" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="text-sm font-semibold text-better-teal uppercase">Safety</span>
                            <h3 class="text-xl font-bold text-better-dark mt-1 mb-3">Annual Pool Maintenance</h3>
                            <p class="text-gray-600">Information regarding upcoming scheduled closures for essential works.</p>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-12">
                    <a href="#" class="inline-block border-2 border-better-dark text-better-dark hover:bg-better-dark hover:text-white font-bold py-3 px-8 rounded-full transition duration-300">
                        View All News
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-better-dark py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <!-- Column 1 -->
                <div>
                    <h4 class="font-bold text-lg mb-4 text-better-teal">About Better</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-better-teal transition">Our Mission</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Careers</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Press & Media</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Contact Us</a></li>
                    </ul>
                </div>
                <!-- Column 2 -->
                <div>
                    <h4 class="font-bold text-lg mb-4 text-better-teal">Help & Info</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-better-teal transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Terms & Conditions</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Site Map</a></li>
                    </ul>
                </div>
                <!-- Column 3 -->
                <div>
                    <h4 class="font-bold text-lg mb-4 text-better-teal">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-better-teal transition">Book an Activity</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Member Login</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Centre Finder</a></li>
                        <li><a href="#" class="hover:text-better-teal transition">Feedback</a></li>
                    </ul>
                </div>
                <!-- Column 4 (Social/Contact) -->
                <div>
                    <h4 class="font-bold text-lg mb-4 text-better-teal">Follow Us</h4>
                    <div class="flex space-x-3">
                        <a href="#" class="p-2 rounded-full border border-white hover:border-better-teal transition">
                            <!-- Placeholder for Social Icon (e.g., Facebook) -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 13.5h2.5l1-4H14v-2c0-1.5 0.73-2.5 2.5-2.5H18V4.14c-.49-.07-2.19-.24-3.5-.24-3.4 0-5.5 2.08-5.5 5.67V13.5H7.5v4H11V22h4v-4.5z"/></svg>
                        </a>
                        <a href="#" class="p-2 rounded-full border border-white hover:border-better-teal transition">
                            <!-- Placeholder for Social Icon (e.g., Twitter/X) -->
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.34-1.6.57-2.48.66.89-.53 1.57-1.37 1.89-2.36-.83.49-1.75.85-2.72 1.05C18.15 4.86 17.1 4 15.93 4c-2.35 0-4.25 1.9-4.25 4.25 0 .33.04.65.12.96C8.5 9.3 5.4 7.7 3.33 5.3c-.36.62-.56 1.34-.56 2.1 0 1.46.74 2.75 1.86 3.52-.69-.02-1.34-.22-1.9-.53v.05c0 2.05 1.46 3.76 3.4 4.14-.36.1-.73.15-1.12.15-.27 0-.53-.03-.79-.08.54 1.7 2.1 2.94 3.96 2.98C8.2 19.34 6.3 20 4.3 20c-.3 0-.6-.02-.9-.06 2.02 1.29 4.4 2.04 6.94 2.04 8.32 0 12.87-6.9 12.87-12.87 0-.2-.01-.4-.01-.6.88-.63 1.64-1.42 2.24-2.31z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright and Footer Bottom -->
            <div class="pt-8 mt-8 border-t border-better-blue-light text-sm text-center">
                <p>&copy; 2024 Better (GLL). All rights reserved. Registered Charity No: 1122858.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Mobile Menu Toggle -->
    <script>
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Simple exponential backoff retry for fetch (optional, but good practice for API calls)
        async function fetchWithRetry(url, options, retries = 3, delay = 1000) {
            for (let i = 0; i < retries; i++) {
                try {
                    const response = await fetch(url, options);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response;
                } catch (error) {
                    if (i === retries - 1) {
                        console.error('Fetch failed after multiple retries:', error);
                        throw error;
                    }
                    await new Promise(resolve => setTimeout(resolve, delay * Math.pow(2, i)));
                }
            }
        }
    </script>
</body>
</html>
