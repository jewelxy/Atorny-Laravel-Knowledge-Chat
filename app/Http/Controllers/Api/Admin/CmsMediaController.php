<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Cms\UploadCmsMediaRequest;

class CmsMediaController extends ApiController
{
    public function upload(UploadCmsMediaRequest $request)
    {
        $path = $request->file('image')->store('cms', 'public');

        return $this->successResponse('CMS media uploaded', [
            'path' => $path,
            'url' => asset('storage/'.$path),
        ], 201);
    }
}
