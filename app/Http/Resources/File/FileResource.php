<?php

namespace App\Http\Resources\File;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this;
        return [
            "id" => $file->id,
            "uuid" => $file->uuid,
            "name" => $file->name,
            "size" => $file->size,
            "mime" => $file->mime,
            "extension" => $file->extension,
            "url" => $file->url(),
            "preview" => $file->preview(),
            "download" => $file->download(),
        ];
    }
}
