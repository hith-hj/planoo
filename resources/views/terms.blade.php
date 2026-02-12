@extends('layouts.main')
@section('content')
    {{-- hero --}}
    <section id="home" class="">
        <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
            <!-- Hero Text -->
            <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                {{ __('Terms & conditions') }}
            </h1>
            <p class="text-white text-xl md:text-3xl mb-8 text-center drop-shadow-md w-full md:w-1/2">
                {{ __('Here we clearify what can and can not be done on our platform') }}
            </p>

        </div>
    </section>

 	<div class="max-w-7xl mx-auto px-6 py-12">
	    <!-- Introduction -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introduction</h2>
	      <p class="leading-relaxed">
	        Welcome to <span class="font-semibold">Planoo</span>. These Terms of Use govern your access to and use of our sports and fitness platform, including mobile applications, websites, and related services. By registering or using our platform, you agree to comply with these Terms.
	      </p>
	    </section>

	    <!-- Eligibility -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Eligibility</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>Users must be at least 16 years old (or the minimum legal age in their jurisdiction).</li>
	        <li>By using the platform, you confirm that you meet eligibility requirements.</li>
	      </ul>
	    </section>

	    <!-- Account Registration -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Account Registration</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>You must provide accurate and complete information during registration.</li>
	        <li>You are responsible for maintaining the confidentiality of your login credentials.</li>
	        <li>Any activity under your account is your responsibility.</li>
	      </ul>
	    </section>

	    <!-- Acceptable Use -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Acceptable Use</h2>
	      <p class="mb-4">You agree not to:</p>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>Post offensive, discriminatory, or harmful content.</li>
	        <li>Use the platform for illegal activities.</li>
	        <li>Attempt to hack, disrupt, or misuse the platform’s services.</li>
	        <li>Disrespect other users or engage in unsportsmanlike behavior.</li>
	      </ul>
	    </section>

	    <!-- Content & Intellectual Property -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Content & Intellectual Property</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>All content, logos, and trademarks on the platform are owned by Planoo or its licensors.</li>
	        <li>You may not copy, distribute, or exploit platform content without prior written consent.</li>
	        <li>User-generated content remains your property, but you grant Planoo a license to use it.</li>
	      </ul>
	    </section>

	    <!-- Services -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Services</h2>
	      <p class="leading-relaxed">
	        The platform may provide training programs, live streams, sports event updates, community forums, and social features. Services may change or be discontinued at any time without prior notice.
	      </p>
	    </section>

	    <!-- Payments -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Payments & Subscriptions</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>Certain features may require paid subscriptions.</li>
	        <li>All payments are non-refundable unless required by law.</li>
	        <li>Users must comply with billing terms and payment schedules.</li>
	      </ul>
	    </section>

	    <!-- Privacy -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Privacy</h2>
	      <p class="leading-relaxed">
	        Your personal data will be handled according to our Privacy Policy. We prioritize user safety and data protection.
	      </p>
	    </section>

	    <!-- Limitation of Liability -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Limitation of Liability</h2>
	      <p class="leading-relaxed">
	        The platform is provided “as is” without warranties. Planoo is not liable for injuries, losses, or damages resulting from sports activities or reliance on platform content.
	      </p>
	    </section>

	    <!-- Termination -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Termination</h2>
	      <p class="leading-relaxed">
	        We reserve the right to suspend or terminate accounts that violate these Terms. Users may terminate their account at any time.
	      </p>
	    </section>

	    <!-- Governing Law -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Governing Law</h2>
	      <p class="leading-relaxed">
	        These Terms are governed by the laws of [Your Jurisdiction]. Any disputes will be resolved in the courts of [Your Jurisdiction].
	      </p>
	    </section>

	    <!-- Changes -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Changes to Terms</h2>
	      <p class="leading-relaxed">
	        We may update these Terms periodically. Continued use of the platform after changes constitutes acceptance of the new Terms.
	      </p>
	    </section>
	</div>

@endsection
