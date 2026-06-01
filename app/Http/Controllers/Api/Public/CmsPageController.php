<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CmsPagePublicResource;
use App\Services\CMS\CmsPageService;
use Illuminate\Http\Request;

class CmsPageController extends ApiController
{
    public function __construct(
        private readonly CmsPageService $cmsPageService
    ) {}

    /**
     * Public headless page payload for frontend page builders.
     *
     * GET /api/v1/public/pages/{key}?locale=es
     */
    public function show(Request $request, string $key)
    {
        $locale = $request->query('locale', app()->getLocale());

        if ($locale) {
            app()->setLocale($locale);
        }

        $page = $this->cmsPageService->getPublishedPageByKey($key, $locale);

        return $this->successResponse('CMS page loaded', new CmsPagePublicResource($page));
    }
}
