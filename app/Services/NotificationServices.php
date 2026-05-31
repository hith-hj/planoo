<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Notifiable;
use App\Models\Notification;
use Illuminate\Support\Collection;

final class NotificationServices
{
    public function all(Notifiable $notifiable): Collection
    {
        $notis = $notifiable->notifications;
        NotFound($notis, 'notifications');

        return $notis->sortByDesc('created_at');
    }

    public function find(int $id): Notification
    {
        Required($id, 'id');
        $noti = Notification::find($id);
        NotFound($noti, 'notification');

        return $noti;
    }

    public function checkNew(Notifiable $notifiable): bool
    {
        return $notifiable->notifications()->where('is_viewed', 0)->count() > 0;
    }

    public function findByNotifiable(Notifiable $notifiable, int $id): Notification
    {
        Required($id, 'id');
        $noti = $notifiable->notifications()->find($id);
        NotFound($noti, 'notification');

        return $noti;
    }

    public function view(array $ids): bool|int
    {
        Required($ids, 'id');

        return Notification::whereIn('id', $ids)->update(['is_viewed' => 1]);
    }

    public function delete(Notification $notification): bool|int
    {
        NotFound($notification, 'notification');

        return $notification->delete();
    }

    public function clear(Notifiable $notifiable)
    {
        $notifiable->notifications()->delete();

        return true;
    }
}
