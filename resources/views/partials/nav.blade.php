<header class="sticky top-0 z-50 border-b border-line bg-white/95 backdrop-blur-lg dark:bg-[#1c1826]/95" data-site-header>
    <div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px] flex min-h-[76px] items-center justify-between gap-4 sm:min-h-[88px] sm:gap-6">
        <x-brand :tagline="true" />
        <nav class="hidden items-center gap-[18px] min-[901px]:flex min-[1051px]:gap-[30px] [&_a]:py-3 [&_a]:text-[.85rem] [&_a]:font-semibold [&_a]:text-muted [&_a:hover]:text-accent [&_a[aria-current]]:text-accent" data-desktop-nav aria-label="{{ __('Main navigation') }}">
            @foreach(['home' => 'Home', 'sections' => 'Explore', 'apps' => 'Apps', 'about' => 'About us'] as $section => $label)
                <a href="{{ route('home') }}#{{ $section }}" data-section="{{ $section }}">{{ __($label) }}</a>
            @endforeach
        </nav>
        <div class="flex items-center gap-[18px] sm:gap-[22px] max-[380px]:gap-2.5">
            
            <a class="flex items-center gap-2 text-[.8rem]" href="{{ route('lang', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}" lang="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}"><i class="bi bi-globe2" aria-hidden="true"></i> {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</a>
            <button id="theme-toggle" class="inline-flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-line bg-transparent text-[1.1rem] text-accent transition-all duration-200 hover:bg-purple/10 active:scale-[.97]" type="button" aria-label="{{ __('Dark mode') }}" aria-pressed="false" title="{{ __('Dark mode') }}"><i class="bi bi-moon" aria-hidden="true"></i></button>
            <div class="hidden min-[1051px]:block"><x-button href="{{ route('home') }}#partners">{{ __('Become Partner') }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></x-button></div>
            <button id="mobile-menu-btn" class="block cursor-pointer border-0 bg-transparent text-[1.8rem] text-accent min-[901px]:hidden" type="button" aria-label="{{ __('Open menu') }}" aria-expanded="false" aria-controls="mobile-menu"><i class="bi bi-list" aria-hidden="true"></i></button>
        </div>
    </div>
    <nav id="mobile-menu" class="pt-3 pb-6 min-[901px]:hidden [&_a]:block [&_a]:border-t [&_a]:border-line [&_a]:py-3 [&:not([hidden])]:animate-[menu-enter_.2s_ease-out] mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]" aria-label="{{ __('Mobile navigation') }}" hidden>
        @foreach(['home' => 'Home', 'sections' => 'Explore', 'apps' => 'Apps', 'about' => 'About us', 'partners' => 'Become Partner', 'contact' => 'Contact'] as $section => $label)
            <a href="{{ route('home') }}#{{ $section }}">{{ __($label) }}</a>
        @endforeach
    </nav>
</header>
