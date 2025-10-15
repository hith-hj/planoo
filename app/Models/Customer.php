<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\NotificationsHandler;
use App\Traits\ReviewHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Customer extends Model
{
    use HasFactory;
    use NotificationsHandler;
    use ReviewHandler;
}
