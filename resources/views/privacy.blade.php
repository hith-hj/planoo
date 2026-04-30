@extends('layouts.main')
@section('content')
    {{-- hero --}}
    <section id="home" class="">
        <div class="relative w-full h-screen hero-bg bg-blend-overlay bg-purple flex flex-col justify-center items-center">
            <!-- Hero Text -->
            <h1 class="text-white text-4xl md:text-6xl font-bold mb-8 text-center drop-shadow-md">
                {{ __('Privacy Policy') }}
            </h1>
            <p class="text-white text-xl md:text-3xl mb-8 text-center drop-shadow-md w-full md:w-1/2">
                {{ __('Here we clearify what can and can not be done on our platform') }}
            </p>

        </div>
    </section>

 	<div class="max-w-7xl mx-auto px-6 py-12">
 		<section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introduction</h2>
	      <p class="leading-relaxed">
	        At <span class="font-semibold">Planoo</span>, we prioritize your privacy. This Privacy Policy explains how we collect, use, and protect your personal data when you use our fitness app and related services.
	      </p>
	    </section>

	    <!-- Data Collection -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Information We Collect</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li><strong>Personal Information:</strong> Such as your name, email address, and date of birth provided during registration.</li>
	        <li><strong>Fitness Data:</strong> Information about your workouts, height, weight, and fitness goals to personalize your experience.</li>
	        <li><strong>Location Data:</strong> We may collect geolocation data to provide outdoor activity tracking features (with your consent).</li>
	      </ul>
	    </section>

	    <!-- How We Use Data -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. How We Use Your Data</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>To provide core app features and customized training plans.</li>
	        <li>To process payments and subscriptions securely.</li>
	        <li>To send notifications and updates regarding your athletic performance or service changes.</li>
	      </ul>
	    </section>

	    <!-- Data Sharing -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Data Sharing</h2>
	      <p class="mb-4">We do not sell your personal data. We may share information with:</p>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>Payment service providers to complete financial transactions.</li>
	        <li>Analytics partners to improve platform performance.</li>
	        <li>Legal authorities if required by law.</li>
	      </ul>
	    </section>

	    <!-- Data Security -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Data Security</h2>
	      <p class="leading-relaxed">
	        We implement advanced technical and administrative measures to protect your data from unauthorized access or loss. However, no method of data transmission over the internet is 100% secure.
	      </p>
	    </section>

	    <!-- User Rights -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. User Rights</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>The right to access and correct your personal data.</li>
	        <li>The right to request the deletion of your account and associated data.</li>
	        <li>The right to withdraw consent for specific data collection (such as location) via app settings.</li>
	      </ul>
	    </section>

	    <!-- Cookies -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Cookies</h2>
	      <p class="leading-relaxed">
	        We use cookies to improve your browsing experience and remember your preferences. You can manage these cookies through your browser settings.
	      </p>
	    </section>

	    <!-- Contact Us -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Contact Us</h2>
	      <p class="leading-relaxed">
	        If you have any questions regarding this Privacy Policy, please contact us at: [support@planoo.com].
	      </p>
	    </section>

 	</div>

 	<div class="max-w-7xl mx-auto px-6 py-12" dir="rtl">

	 	<section class="mb-10" >
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. مقدمة</h2>
	      <p class="leading-relaxed">
	        نحن في <span class="font-semibold">Planoo</span> نولي أهمية كبرى لخصوصيتك. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية بياناتك الشخصية عند استخدام تطبيقنا وخدماتنا المتعلقة باللياقة البدنية.
	      </p>
	    </section>

	    <!-- Data Collection -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. المعلومات التي نجمعها</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li><strong>المعلومات الشخصية:</strong> مثل الاسم، البريد الإلكتروني، وتاريخ الميلاد عند التسجيل.</li>
	        <li><strong>بيانات اللياقة البدنية:</strong> معلومات حول تمارينك، الطول، الوزن، والأهداف الرياضية لتحسين تجربتك.</li>
	        <li><strong>بيانات الموقع:</strong> قد نجمع معلومات الموقع الجغرافي لتوفير ميزات تتبع النشاط الخارجي (بموافقتك).</li>
	      </ul>
	    </section>

	    <!-- How We Use Data -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. كيفية استخدام بياناتك</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>تقديم ميزات التطبيق الأساسية وخطط التدريب المخصصة.</li>
	        <li>معالجة المدفوعات والاشتراكات بشكل آمن.</li>
	        <li>إرسال إشعارات وتحديثات تتعلق بأدائك الرياضي أو تغييرات الخدمة.</li>
	      </ul>
	    </section>

	    <!-- Data Sharing -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. مشاركة البيانات</h2>
	      <p class="mb-4">نحن لا نبيع بياناتك الشخصية. قد نشارك المعلومات مع:</p>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>مزودي خدمات الدفع لإتمام العمليات المالية.</li>
	        <li>شركاء التحليلات لتحسين أداء التطبيق.</li>
	        <li>الجهات القانونية إذا كان ذلك مطلوباً بموجب القانون.</li>
	      </ul>
	    </section>

	    <!-- Data Security -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. أمن البيانات</h2>
	      <p class="leading-relaxed">
	        نطبق إجراءات تقنية وإدارية متقدمة لحماية بياناتك من الوصول غير المصرح به أو الفقدان. ومع ذلك، لا يمكن ضمان أمان نقل البيانات عبر الإنترنت بنسبة 100%.
	      </p>
	    </section>

	    <!-- User Rights -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. حقوق المستخدم</h2>
	      <ul class="list-disc pl-6 space-y-2">
	        <li>الحق في الوصول إلى بياناتك الشخصية وتصحيحها.</li>
	        <li>الحق في طلب حذف حسابك وبياناتك المرتبطة به.</li>
	        <li>الحق في سحب الموافقة على جمع بيانات معينة (مثل الموقع) عبر إعدادات التطبيق.</li>
	      </ul>
	    </section>

	    <!-- Cookies -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. ملفات تعريف الارتباط (Cookies)</h2>
	      <p class="leading-relaxed">
	        نستخدم ملفات تعريف الارتباط لتحسين تصفحك وتذكر تفضيلاتك. يمكنك إدارة خيارات هذه الملفات من خلال إعدادات متصفحك.
	      </p>
	    </section>

	    <!-- Contact Us -->
	    <section class="mb-10">
	      <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. اتصل بنا</h2>
	      <p class="leading-relaxed">
	        إذا كان لديك أي استفسارات حول سياسة الخصوصية هذه، يرجى التواصل معنا عبر البريد الإلكتروني: [support@planoo.com].
	      </p>
	    </section>
	</div>

@endsection
