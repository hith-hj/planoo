<div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3 min-[901px]:gap-6">
@foreach([['bi-eye', 'Our Vision', 'To empower communities by promoting wellness, education, and social connection.'], ['bi-compass', 'Our Mission', 'To provide accessible facilities and programs that inspire healthier lifestyles.'], ['bi-heart', 'Our Values', 'Integrity, inclusivity, and innovation guide everything we do.']] as [$icon, $title, $description])
<article class="rounded-[14px] border border-line p-[22px] min-[901px]:p-[30px] [&_p]:mt-3.5 [&_p]:text-[.88rem] transition-[opacity,transform] duration-500 data-[reveal=pending]:translate-y-[18px] data-[reveal=pending]:opacity-0 focus-within:translate-y-0 focus-within:opacity-100" data-reveal>
<span class="mb-6 flex size-12 items-center justify-center rounded-xl bg-[#edf5f1] text-2xl text-teal dark:bg-[#233e36]">
<i class="bi {{ $icon }}" aria-hidden="true">
</i>
</span>
<h3>{{ __($title) }}</h3>
<p>{{ __($description) }}</p>
</article>
@endforeach
</div>
