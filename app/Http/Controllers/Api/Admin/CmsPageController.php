<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Cms\StoreCmsPageRequest;
use App\Http\Requests\Cms\UpdateCmsPageRequest;
use App\Http\Resources\CmsPageAdminResource;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsPageController extends ApiController
{
    public function index()
    {
        $pages = CmsPage::query()
            ->with(['sections.translations'])
            ->orderBy('key')
            ->get();

        return $this->successResponse('CMS pages loaded', [
            'pages' => CmsPageAdminResource::collection($pages),
        ]);
    }

    public function store(StoreCmsPageRequest $request)
    {
        $page = CmsPage::create([
            ...$request->validated(),
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return $this->successResponse('CMS page created', [
            'page' => new CmsPageAdminResource($page),
        ], 201);
    }

    public function show(CmsPage $page)
    {
        $page->load(['sections.translations']);

        return $this->successResponse('CMS page loaded', [
            'page' => new CmsPageAdminResource($page),
        ]);
    }

    public function update(UpdateCmsPageRequest $request, CmsPage $page)
    {
        $page->update([
            ...$request->validated(),
            'updated_by' => $request->user()?->id,
        ]);

        $page->load(['sections.translations']);

        return $this->successResponse('CMS page updated', [
            'page' => new CmsPageAdminResource($page),
        ]);
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();

        return $this->successResponse('CMS page deleted');
    }
}
