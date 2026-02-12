<footer id="contact" class="hero-bg h-fit md:h-full bg-blend-multiply bg-purple text-white">
    <div class="flex justify-center items-start py-10">
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
                <button class="w-full md:w-auto bg-yellow/80 text-purple hover:bg-yellow font-medium py-4 px-8 rounded-lg transition duration-300 flex items-center justify-center space-x-2 shadow-md">
                    <span>{{ __('Contact') }}</span>
                    <i class="bi bi-arrow-right px-2 rtl:rotate-180"></i>
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
                    {{ __('We are dedicated to creating vibrant spaces where health, learning, and community come together.') }}
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
                    <li><a href="{{route('terms')}}" class="hover:text-gold transition">{{ __('Terms & Condition') }}</a></li>
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
