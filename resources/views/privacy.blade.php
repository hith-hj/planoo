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
    	<!-- Section 1 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Definitions and Interpretation</h2>
		  <p class="leading-relaxed mb-2">In this Policy, the following terms shall have the following meanings:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li><span class="font-semibold">“Data”</span> means all information that you submit to PLANOO via the Platform.</li>
		    <li><span class="font-semibold">“User”</span> means any individual who uses the Platform and is not employed by PLANOO.</li>
		    <li><span class="font-semibold">“Platform”</span> means the PLANOO mobile application and website.</li>
		    <li><span class="font-semibold">“Partners”</span> means third-party service providers (such as clubs, trainers, or venues).</li>
		  </ul>
		</section>

		<!-- Section 2 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Scope of this Policy</h2>
		  <p class="leading-relaxed mb-2">This Policy applies only to the actions of PLANOO and Users within the Platform. It does not extend to:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>Any external websites or services</li>
		    <li>Any links directing to other platforms</li>
		    <li>Services provided directly by Partners</li>
		  </ul>
		  <p class="leading-relaxed mt-2 italic">Users are advised to review the privacy policies of those third parties.</p>
		</section>

		<!-- Section 3 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Data Collected</h2>
		  <p class="leading-relaxed mb-2">We may collect the following Data:</p>

		  <p class="font-semibold mt-2">Personal Data:</p>
		  <ul class="list-disc ml-6 mb-2">
		    <li>Name</li>
		    <li>Email address</li>
		    <li>Phone number</li>
		    <li>Account details</li>
		  </ul>

		  <p class="font-semibold mt-2">Usage Data:</p>
		  <ul class="list-disc ml-6 mb-2">
		    <li>Booking history</li>
		    <li>Activity within the app</li>
		    <li>Preferences</li>
		  </ul>

		  <p class="font-semibold mt-2">Technical Data:</p>
		  <ul class="list-disc ml-6">
		    <li>Device type and operating system</li>
		    <li>Browser type (for website use)</li>
		    <li>Usage logs</li>
		  </ul>
		</section>

		<!-- Section 4 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Legal Basis for Processing (GDPR)</h2>
		  <p class="leading-relaxed mb-2">Data is processed based on one of the following legal grounds:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>User consent</li>
		    <li>Performance of a contract (e.g., bookings)</li>
		    <li>Legitimate interests (service improvement and security)</li>
		    <li>Legal obligations</li>
		  </ul>
		</section>

		<!-- Section 5 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Use of Data</h2>
		  <p class="leading-relaxed mb-2">Data may be used for the following purposes:</p>
		  <ul class="list-disc ml-6 leading-relaxed mb-4">
		    <li>Managing user accounts and bookings</li>
		    <li>Improving services and user experience</li>
		    <li>Connecting Users with Partners</li>
		    <li>Sending notifications and offers</li>
		    <li>Conducting market research</li>
		    <li>Preventing fraud and ensuring platform security</li>
		  </ul>
		  <p class="leading-relaxed font-semibold text-red-600"> PLANOO does not sell personal data to any third party</p>
		</section>

		<!-- Section 6 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Data Sharing</h2>
		  <p class="leading-relaxed mb-2">Data may be shared with:</p>
		  <ul class="list-disc ml-6 leading-relaxed mb-4">
		    <li>Partners: to complete bookings and provide services</li>
		    <li>Service providers: such as hosting and technical support</li>
		    <li>Legal authorities: if required by law</li>
		  </ul>
		  <p class="leading-relaxed italic"> PLANOO is not responsible for how Partners use data outside the Platform</p>
		</section>

		<!-- Section 7 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. International Data Transfers</h2>
		  <p class="leading-relaxed">Data may be transferred outside the user’s country. We take appropriate safeguards in accordance with GDPR.</p>
		</section>

		<!-- Section 8 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Data Retention</h2>
		  <p class="leading-relaxed mb-2">Data is retained:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>As long as the account is active</li>
		    <li>Or as required by applicable laws</li>
		  </ul>
		</section>

		<!-- Section 9 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. User Rights (GDPR)</h2>
		  <p class="leading-relaxed mb-2">Users have the right to:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>Access their data</li>
		    <li>Correct their data</li>
		    <li>Request deletion (“Right to be Forgotten”)</li>
		    <li>Restrict or object to processing</li>
		    <li>Withdraw consent</li>
		    <li>Request data portability</li>
		  </ul>
		  <p class="mt-2"> Requests can be made via contact</p>
		</section>

		<!-- Section 10 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Account Deletion</h2>
		  <p class="leading-relaxed mb-2">Users may request deletion of their account and data:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>Through the app</li>
		    <li>Or by contacting us</li>
		  </ul>
		  <p class="mt-2 italic">Requests will be processed within a reasonable timeframe.</p>
		</section>

		<!-- Section 11 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Links to Other Websites</h2>
		  <p class="leading-relaxed">The Platform may contain links to external websites. PLANOO is not responsible for their content or privacy practices.</p>
		</section>

		<!-- Section 12 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Data Security</h2>
		  <p class="leading-relaxed mb-2">We take data security seriously and implement:</p>
		  <ul class="list-disc ml-6 leading-relaxed">
		    <li>Technical measures</li>
		    <li>Organizational measures</li>
		    <li>Appropriate safeguards</li>
		  </ul>
		  <p class="mt-2 italic">However, no system can guarantee complete security.</p>
		</section>

		<!-- Section 13 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">13. Children’s Privacy</h2>
		  <p class="leading-relaxed">The Platform is not intended for children without legal supervision, and we do not knowingly collect their data.</p>
		</section>

		<!-- Section 14 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">14. Policy Updates</h2>
		  <p class="leading-relaxed">PLANOO may update this Policy at any time. Continued use of the Platform constitutes acceptance of any updates.</p>
		</section>

		<!-- Section 15 -->
		<section class="mb-10">
		  <h2 class="text-2xl font-semibold text-gray-900 mb-4">15. Contact Information</h2>
		  <p class="leading-relaxed"> info@planoo.net</p>
		  <p class="leading-relaxed"> www.planoo.net</p>
		</section>

		<h1> © Copyright </h1>
		<p>
			All content and design rights are reserved by PLANOO.
		</p>
    </div>


    <div class="max-w-7xl mx-auto px-6 py-12" dir="rtl">
		<!-- Section 1 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">1. التعريفات والتفسير</h2>
			<p class="leading-relaxed mb-2">في هذه السياسة، يكون للمصطلحات التالية المعاني الموضحة أدناه:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li><span class="font-semibold">”البيانات“:</span> تعني جميع المعلومات التي يقدمها المستخدم إلى PLANOO عبر المنصة.</li>
			  <li><span class="font-semibold">”المستخدم“:</span> أي شخص يستخدم المنصة وليس موظفاً لدى PLANOO.</li>
			  <li><span class="font-semibold">”المنصة“:</span> تطبيق الهاتف المحمول والموقع الإلكتروني الخاص بـ PLANOO.</li>
			  <li><span class="font-semibold">”العملاء“:</span> مقدمو الخدمات من الأطراف الثالثة (مثل الأندية، المدربين، أو المنشآت).</li>
			</ul>
		</section>

		<!-- Section 2 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">2. نطاق هذه السياسة</h2>
			<p class="leading-relaxed mb-2">تنطبق هذه السياسة فقط على تعامل PLANOO والمستخدمين داخل المنصة. ولا تمتد إلى:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>أي مواقع أو خدمات خارجية</li>
			  <li>أي روابط تؤدي إلى مواقع أخرى</li>
			  <li>الخدمات المقدمة مباشرة من قبل العملاء</li>
			</ul>
			<p class="leading-relaxed mt-2 italic">ويُنصح المستخدمون بمراجعة سياسات الخصوصية الخاصة بتلك الجهات.</p>
		</section>

		<!-- Section 3 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">3. البيانات التي يتم جمعها</h2>
			<p class="leading-relaxed mb-2">قد نقوم بجمع البيانات التالية:</p>

			<p class="font-semibold mt-2">بيانات شخصية:</p>
			<ul class="list-disc mr-6 mb-2">
			  <li>الاسم</li>
			  <li>البريد الإلكتروني</li>
			  <li>رقم الهاتف</li>
			  <li>بيانات الحساب</li>
			</ul>

			<p class="font-semibold mt-2">بيانات الاستخدام:</p>
			<ul class="list-disc mr-6 mb-2">
			  <li>سجل الحجوزات</li>
			  <li>التفاعل داخل التطبيق</li>
			  <li>التفضيلات</li>
			</ul>

			<p class="font-semibold mt-2">بيانات تقنية:</p>
			<ul class="list-disc mr-6">
			  <li>نوع الجهاز ونظام التشغيل</li>
			  <li>نوع المتصفح (في حال استخدام الموقع)</li>
			  <li>بيانات الاستخدام والتسجيل (Logs)</li>
			</ul>
		</section>

		<!-- Section 4 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">4. الأساس القانوني لمعالجة البيانات (GDPR)</h2>
			<p class="leading-relaxed mb-2">تتم معالجة البيانات وفقاً لأحد الأسس التالية:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>موافقة المستخدم</li>
			  <li>تنفيذ عقد (مثل الحجوزات)</li>
			  <li>المصلحة المشروعة (تحسين الخدمة والأمان)</li>
			  <li>الالتزامات القانونية</li>
			</ul>
		</section>

		<!-- Section 5 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">5. استخدام البيانات</h2>
			<p class="leading-relaxed mb-2">قد يتم استخدام البيانات للأغراض التالية:</p>
			<ul class="list-disc mr-6 leading-relaxed mb-4">
			  <li>إدارة الحسابات والحجوزات</li>
			  <li>تحسين الخدمات وتجربة المستخدم</li>
			  <li>ربط المستخدمين بالعملاء</li>
			  <li>إرسال الإشعارات والعروض</li>
			  <li>إجراء أبحاث السوق</li>
			  <li>حماية المنصة ومنع الاحتيال</li>
			</ul>
			<p class="leading-relaxed font-semibold"> لا تقوم PLANOO ببيع البيانات لأي طرف ثالث</p>
		</section>

		<!-- Section 6 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">6. مشاركة البيانات</h2>
			<p class="leading-relaxed mb-2">قد تتم مشاركة البيانات مع:</p>
			<ul class="list-disc mr-6 leading-relaxed mb-4">
			  <li>العملاء: لتنفيذ الحجوزات وتقديم الخدمات</li>
			  <li>مزودي الخدمات: مثل الاستضافة والدعم التقني</li>
			  <li>الجهات القانونية: إذا طُلب ذلك بموجب القانون</li>
			</ul>
			<p class="leading-relaxed italic text-sm text-gray-600"> لا تتحمل PLANOO مسؤولية استخدام البيانات من قبل العملاء خارج نطاق المنصة</p>
		</section>

		<!-- Section 7 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">7. نقل البيانات دولياً</h2>
			<p class="leading-relaxed">قد يتم نقل البيانات خارج بلد المستخدم، ونلتزم باتخاذ الإجراءات اللازمة لحمايتها وفقاً لقوانين GDPR.</p>
		</section>

		<!-- Section 8 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">8. مدة الاحتفاظ بالبيانات</h2>
			<p class="leading-relaxed mb-2">يتم الاحتفاظ بالبيانات:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>طالما الحساب نشط</li>
			  <li>أو وفق المتطلبات القانونية</li>
			</ul>
		</section>

		<!-- Section 9 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">9. حقوق المستخدم (GDPR)</h2>
			<p class="leading-relaxed mb-2">يحق للمستخدم:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>الوصول إلى بياناته</li>
			  <li>تعديلها</li>
			  <li>طلب حذفها (الحق في النسيان)</li>
			  <li>تقييد أو الاعتراض على المعالجة</li>
			  <li>سحب الموافقة</li>
			  <li>طلب نقل البيانات</li>
			</ul>
			<p class="mt-2">عبر التواصل معنا</p>
		</section>

		<!-- Section 10 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">10. حذف الحساب</h2>
			<p class="leading-relaxed mb-2">يمكن للمستخدم طلب حذف حسابه وبياناته:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>من داخل التطبيق</li>
			  <li>أو عبر التواصل معنا</li>
			</ul>
			<p class="mt-2 italic">وسيتم تنفيذ الطلب خلال فترة زمنية مناسبة.</p>
		</section>

		<!-- Section 11 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">11. روابط لمواقع أخرى</h2>
			<p class="leading-relaxed">قد تحتوي المنصة على روابط لمواقع خارجية، ولا تتحمل PLANOO مسؤولية محتوى أو سياسات تلك المواقع.</p>
		</section>

		<!-- Section 12 -->
		<section class="mb-12">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">12. أمان البيانات</h2>
			<p class="leading-relaxed mb-2">نولي أمان البيانات أهمية كبيرة، ونستخدم:</p>
			<ul class="list-disc mr-6 leading-relaxed">
			  <li>إجراءات تقنية</li>
			  <li>إجراءات تنظيمية</li>
			  <li>وسائل حماية مناسبة</li>
			</ul>
			<p class="mt-2 italic">ومع ذلك، لا يمكن ضمان الأمان الكامل.</p>
		</section>

		<!-- Section 13 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">13. خصوصية الأطفال</h2>
			<p class="leading-relaxed">لا تستهدف المنصة الأطفال دون إشراف قانوني، ولا يتم جمع بياناتهم بشكل متعمد.</p>
		</section>

		<!-- Section 14 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">14. تعديل السياسة</h2>
			<p class="leading-relaxed">يحق لـ PLANOO تعديل هذه السياسة في أي وقت، ويُعد استمرار استخدام المنصة موافقة على التعديلات.</p>
		</section>

		<!-- Section 15 -->
		<section class="mb-10">
			<h2 class="text-2xl font-semibold text-gray-900 mb-4">15. معلومات التواصل</h2>
			<p class="leading-relaxed"> info@planoo.net</p>
			<p class="leading-relaxed"> www.planoo.net</p>
		</section>

		<h1>
			©️ حقوق الملكية
		</h1>
		<p>
			جميع حقوق المحتوى والتصميم محفوظة لمنصة PLANOO.
		</p>
    </div>

@endsection
