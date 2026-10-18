@props(['href', 'icon', 'name'])
<a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg bg-ink px-[13px] py-2.5 text-[.85rem] leading-tight text-white transition-all duration-200 hover:bg-purple active:scale-[.97] sm:px-[17px] sm:text-[.95rem] dark:bg-[#383043] dark:hover:bg-[#7050bd]">
    <i class="bi {{ $icon }} text-[1.6rem]" aria-hidden="true"></i>
    <span><small class="mb-[3px] block text-[.58rem] opacity-75">{{ __('Download on') }}</small>{{ $name }}</span>
    <span class="sr-only">{{ __('opens in a new tab') }}</span>
</a>
