<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Cms\StoreCmsSectionRequest;
use App\Http\Requests\Cms\UpdateCmsSectionRequest;
use App\Http\Resources\CmsSectionAdminResource;
use App\Models\CmsSection;
use Illuminate\Http\Request;

class CmsSectionController extends ApiController
{
    public function index(Request $request)
    {
        $sections = CmsSection::query()
            ->when(
                $request->filled('cms_page_id'),
                fn ($q) => $q->where('cms_page_id', $request->integer('cms_page_id'))
            )
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse('CMS sections loaded', [
            'sections' => CmsSectionAdminResource::collection($sections),
        ]);
    }

    public function store(StoreCmsSectionRequest $request)
    {
        $section = CmsSection::create($request->validated());
        $section->load('translations');

        return $this->successResponse('CMS section created', [
            'section' => new CmsSectionAdminResource($section),
        ], 201);
    }

    public function show(CmsSection $section)
    {
        $section->load('translations');

        return $this->successResponse('CMS section loaded', [
            'section' => new CmsSectionAdminResource($section),
        ]);
    }

    public function update(UpdateCmsSectionRequest $request, CmsSection $section)
    {
        $section->update($request->validated());
        $section->load('translations');

        return $this->successResponse('CMS section updated', [
            'section' => new CmsSectionAdminResource($section),
        ]);
    }

    public function destroy(CmsSection $section)
    {
        $section->delete();

        return $this->successResponse('CMS section deleted');
    }
}
