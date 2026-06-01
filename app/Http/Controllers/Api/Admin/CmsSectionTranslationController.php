<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Cms\StoreCmsSectionTranslationRequest;
use App\Http\Requests\Cms\UpdateCmsSectionTranslationRequest;
use App\Http\Resources\CmsSectionTranslationResource;
use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;

class CmsSectionTranslationController extends ApiController
{
    public function index(CmsSection $section)
    {
        return $this->successResponse('CMS section translations retrieved', [
            'cms_section_id' => $section->id,
            'translations' => CmsSectionTranslationResource::collection($section->translations),
        ]);
    }

    public function store(StoreCmsSectionTranslationRequest $request, CmsSection $section)
    {
        $translation = CmsSectionTranslation::updateOrCreate(
            [
                'cms_section_id' => $section->id,
                'locale' => $request->validated('locale'),
            ],
            ['data' => $request->validated('data')]
        );

        return $this->successResponse('CMS section translation saved', [
            'translation' => new CmsSectionTranslationResource($translation),
        ], 201);
    }

    public function show(CmsSection $section, CmsSectionTranslation $translation)
    {
        if ($translation->cms_section_id !== $section->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        return $this->successResponse('CMS section translation loaded', [
            'translation' => new CmsSectionTranslationResource($translation),
        ]);
    }

    public function update(UpdateCmsSectionTranslationRequest $request, CmsSection $section, CmsSectionTranslation $translation)
    {
        if ($translation->cms_section_id !== $section->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        $translation->update(['data' => $request->validated('data')]);

        return $this->successResponse('CMS section translation updated', [
            'translation' => new CmsSectionTranslationResource($translation),
        ]);
    }

    public function destroy(CmsSection $section, CmsSectionTranslation $translation)
    {
        if ($translation->cms_section_id !== $section->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        $translation->delete();

        return $this->successResponse('CMS section translation deleted');
    }
}
