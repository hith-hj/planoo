<?php

declare(strict_types=1);

return [
    'activity_id' => [
        'required' => 'يرجى اختيار نشاط صالح.',
        'exists' => 'النشاط المحدد غير موجود.',
    ],
    'category_id' => [
        'required' => 'الفئة مطلوبة.',
        'exists' => 'الفئة المختارة غير صالحة.',
    ],
    'name' => [
        'required' => 'لا يمكن أن يكون اسم النشاط فارغاً.',
    ],
    'description' => [
        'required' => 'يرجى تقديم وصف للنشاط.',
        'max' => 'يجب ألا يتجاوز الوصف 1500 حرف.',
    ],
    'price' => [
        'required' => 'يجب تحديد السعر.',
        'numeric' => 'يجب أن يكون السعر رقماً.',
        'min' => 'يجب أن يكون السعر :min على الأقل.',
    ],
    'session_duration' => [
        'required' => 'يرجى اختيار مدة جلسة صالحة.',
    ],
];
