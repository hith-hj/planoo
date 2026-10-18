<section id="apps" class="bg-soft py-[60px] sm:py-24">
<div class="mx-auto w-[calc(100%-40px)] sm:w-[calc(100%-48px)] min-[901px]:w-[calc(100%-80px)] max-w-[1200px]">
<div class="mb-9 flex flex-col items-start justify-between gap-5 sm:flex-row sm:gap-9 min-[901px]:items-end [&>p]:text-[.9rem] sm:[&>p]:max-w-[280px] min-[901px]:[&>p]:max-w-[360px] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<div>
<span class="mb-[18px] block text-[.69rem] font-bold tracking-[.16em] text-teal rtl:tracking-normal">{{ __('YOUR PLANS. IN YOUR POCKET.') }}</span>
<h2>{{ __('One app. More possibilities.') }}</h2>
</div>
<p>{{ __('Here you can find our applications') }}</p>
</div>
<div class="grid grid-cols-1 gap-[26px] sm:grid-cols-2">
@foreach([
    ['Customers', 'Find your next activity, wherever you are.', 'user-logo-new.jpeg', 'https://play.google.com/store/apps/details?id=com.your1site.planoo', 'https://apps.apple.com/nl/app/planoo/id6767510401'],
    ['Partners', 'Bring your business closer to an active community.', 'partner-logo-new.jpeg', 'https://play.google.com/store/apps/details?id=com.your1site.planoo_partner', 'https://apps.apple.com/us/app/planoo-partner/id6761179417']
] as [$audience, $description, $logo, $android, $ios])
<article class="scroll-mt-[110px] rounded-2xl border border-line bg-surface p-6 sm:p-8 [&_h3]:text-2xl [&_p]:mt-3.5 [&_p]:mb-[26px] [&_p]:text-[.9rem] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal @if($audience === 'Partners') id="partners" @endif>
<div class="mb-7 flex items-center justify-between [&_img]:size-16 [&_img]:rounded-[15px]">
<img src="{{ asset('images/'.$logo) }}" alt="{{ __('Planoo').' '.__($audience) }}" width="64" height="64" loading="lazy">
<span class="rounded-[30px] bg-[#f0ecf8] px-[13px] py-[5px] text-[.72rem] text-accent dark:bg-[#382c50]">{{ __($audience) }}</span>
</div>
<h3>{{ $audience === 'Partners' ? __('Grow with Planoo.') : __('Your next plan starts here.') }}</h3>
<p>{{ __($description) }}</p>
<div class="flex flex-wrap gap-2 sm:gap-3">
<x-store-link :href="$android" icon="bi-google-play" name="Google Play" />
<x-store-link :href="$ios" icon="bi-apple" name="App Store" />
</div>
</article>
@endforeach
</div>
</div>
</section>
