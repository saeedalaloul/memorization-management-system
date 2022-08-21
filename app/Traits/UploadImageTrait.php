<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($photo,$name,$id)
    {
        $photo->storeAs('/', $name, $disk = 'users_images');
        User::find($id)->update([
            'profile_photo' => $name,
        ]);
    }

    public function deleteImage($name)
    {
        Storage::disk('users_images')->delete($name);
    }
}
