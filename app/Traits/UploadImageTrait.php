<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($photo,$name,$id)
    {
        if ($photo != null && $name != null && $id != null) {
            $photo->storeAs('/', $name, $disk = 'users_images');
            User::find($id)->update([
                'profile_photo' => $name,
            ]);
        }
    }

    public function deleteImage($name)
    {
        if ($name != null) {
            Storage::disk('users_images')->delete($name);
        }
    }
}
