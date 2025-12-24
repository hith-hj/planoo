<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="h-18 flex items-center justify-between md:justify-around">

        <!-- Logo -->
        <a href="#" class="flex flex-col items-start group">
            <span class="text-4xl font-bold tracking-tight text-purple group-hover:opacity-90">
                Planoo<span class="text-teal">.</span>
            </span>
            <span class="text-md uppercase text-gray-500 font-semibold tracking-widest hidden md:block">
                {{ __('get better') }}
            </span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-8 font-bold text-purple ltr:text-xl">
            <a href="/#home" data-section="home" class="hover:text-teal py-2">{{ __('Home') }}</a>
            <a href="/#sections" data-section="sections" class="hover:text-teal py-2">{{ __('Sections') }}</a>
            <a href="/#apps" data-section="apps" class="hover:text-teal py-2">{{ __('Apps') }}</a>
            <a href="/#about" data-section="about" class="hover:text-teal py-2">{{ __('About us') }}</a>
            <a href="/#contact" data-section="contact" class="hover:text-teal py-2">{{ __('Contact') }}</a>
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
            <a href="#apps" class="hidden sm:block font-bold text-purple hover:underline">
                {{ __('Become Partner') }}
            </a>

            <!-- Join Button -->
            <a href="#apps" class="hidden sm:inline-block bg-teal hover:bg-tealhover text-white font-bold py-3 px-6 rounded-full transition-colors">
                {{ __('Download App') }}
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
            <a href="#home" data-section="home" class="block py-2 border-b hover:text-teal py-2">
                {{ __('Home') }}
            </a>
            <a href="#sections" data-section="sections" class="block py-2 border-b hover:text-teal py-2">
                {{ __('Sections') }}
            </a>
            <a href="#apps" data-section="apps" class="block py-2 border-b hover:text-teal py-2">
                {{ __('Apps') }}
            </a>
            <a href="#about" data-section="about" class="block py-2 border-b hover:text-teal py-2">
                {{ __('About us') }}
            </a>
            <a href="#contact" data-section="contact" class="block py-2 border-b hover:text-teal py-2">
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
