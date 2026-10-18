<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Notifiable;
use App\Models\Notification;
use App\Support\Pagination;
use Illuminate\Support\Collection;

final class NotificationServices
{
    public function all(Notifiable $notifiable, int $page = 1, ?int $perPage = null): Collection
    {
        Pagination::validate($page, $perPage ?? Pagination::MAX_PER_PAGE);
        $query = $notifiable->notifications()->orderByDesc('created_at')->orderByDesc('id');
        $notis = $perPage === null ? Pagination::bounded($query) : $query->forPage($page, $perPage)->get();
        NotFound($notis, 'notifications');

        return $notis;
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
        return $notifiable->notifications()->where('is_viewed', 0)->exists();
    }

    public function findByNotifiable(Notifiable $notifiable, int $id): Notification
    {
        Required($id, 'id');
        $noti = $notifiable->notifications()->find($id);
        NotFound($noti, 'notification');

        return $noti;
    }

    public function view(Notifiable $notifiable, array $ids): bool|int
    {
        Required($ids, 'id');

        return $notifiable->notifications()->whereIn('id', $ids)->update(['is_viewed' => 1]);
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
