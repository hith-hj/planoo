@props(['variant' => 'primary'])
<a {{ $attributes->class([
    'inline-flex items-center justify-center gap-[22px] rounded-[9px] px-6 py-[15px] text-sm font-semibold transition-all duration-200 hover:-translate-y-0.5 active:scale-[.97]',
    'bg-purple text-white hover:bg-[#35206f] dark:bg-[#7050bd] dark:hover:bg-[#8060cd]' => $variant === 'primary',
    'bg-yellow text-[#30204e]' => $variant === 'yellow',
]) }}>{{ $slot }}</a>
