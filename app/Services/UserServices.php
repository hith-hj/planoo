<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class UserServices
{
    public function get(?int $id): Model
    {
        Required($id, 'user id');
        $user = User::find($id);
        NotFound($user, 'user');

        return $user->load(['medias']);
    }

    public function update(User $user, array $data): bool
    {
        Required($data, 'data');

        return $user->update($data);
    }

    public function delete(User $user, array $data): bool
    {
        return false;
    }

    public function uploadProfileImage(User $user, array $data)
    {
        $oldMedia = $user->mediaByName('profile_image');
        if ($oldMedia !== null) {
            $this->deleteProfileImage($oldMedia);
        }

        return $user->uploadMedia('image', 'profile_image', $data['profile_image']);
    }

    public function deleteProfileImage(Media $media)
    {
        NotFound($media, 'Media');
        Storage::disk('public')->delete($media->url);

        return $media->delete();
    }
}
