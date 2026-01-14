<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AttendHandler
{
    public function isAttending()
    {
        return $this->customers()->where('customer_id', Auth::id());
    }
}
