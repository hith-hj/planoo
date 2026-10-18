@props(['footer' => false, 'tagline' => false])
<a href="{{ route('home') }}" aria-label="{{ __('Planoo home') }}" data-brand
   {{ $attributes->class(['inline-block text-[1.8rem] leading-none font-extrabold tracking-[-.07em] sm:text-[2rem]', 'text-white' => $footer, 'text-accent' => !$footer]) }}>
    Planoo<span class="text-teal">.</span>
    @if($tagline)<small class="mt-2 block text-[.5rem] font-semibold tracking-[.25em] uppercase">{{ __('get better') }}</small>@endif
</a>
