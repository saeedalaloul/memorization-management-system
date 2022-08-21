<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait AttachFilesTrait
{
    public function uploadFile($file,$name,$folder)
    {
        $file->file($name)->storeAs('attachments/',$folder.'/'.$file,'upload_attachments');

    }

    public function deleteFile($name)
    {
        $exists = Storage::disk('upload_attachments')->exists('attachments/'.$name);

        if($exists)
        {
            Storage::disk('upload_attachments')->delete('attachments/'.$name);
        }
    }
}
