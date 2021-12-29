<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait AttachFilesTrait
{
    public function uploadFile($file,$name,$folder)
    {
        $file_name = $file->file($name)->getClientOriginalName();
        $file->file($name)->storeAs('attachments/',$folder.'/'.$file_name,'upload_attachments');

    }

    public function deleteFile($name)
    {
        $exists = Storage::disk('upload_attachments')->exists('attachments/library/'.$name);

        if($exists)
        {
            Storage::disk('upload_attachments')->delete('attachments/library/'.$name);
        }
    }
}
