@props(['category', 'label', 'title', 'description', 'image', 'icon'])
<article class="group overflow-hidden rounded-2xl border border-line bg-surface transition-[opacity,transform,box-shadow] duration-300 hover:-translate-y-1 hover:shadow-[0_14px_35px_#30204e0c] data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal data-category="{{ $category }}">
<a href="#apps" class="relative block overflow-hidden [&_img]:h-[240px] sm:[&_img]:h-[225px] [&_img]:w-full [&_img]:transition-transform [&_img]:duration-[400ms] group-hover:[&_img]:scale-[1.04]" aria-label="{{ __($title) }} — {{ __('Get the app') }}">
<img src="{{ asset('images/'.$image) }}" alt="{{ __($label) }}" loading="lazy" width="600" height="400">
<span class="absolute bottom-4 start-4 flex items-center gap-2 rounded-md bg-surface px-3 py-1.5 text-[.7rem] font-semibold [&_i]:text-teal">
<i class="bi {{ $icon }}" aria-hidden="true">
</i> {{ __($label) }}</span>
<span class="absolute top-3.5 end-3.5 size-8 rounded-full bg-surface text-center" aria-hidden="true">↗</span>
</a>
<div class="p-6 [&_h3]:text-[1.1rem] [&_p]:mt-3 [&_p]:mb-5 [&_p]:text-[.85rem] [&_p]:leading-[1.75]">
<h3>
<a href="#apps">{{ __($title) }}</a>
</h3>
<p>{{ __($description) }}</p>
<x-text-link href="#apps">{{ __('Explore in the app') }} <span aria-hidden="true">↗</span>
</x-text-link>
</div>
</article>
