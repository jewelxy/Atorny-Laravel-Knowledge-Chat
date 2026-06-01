<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\BlogTranslationController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\FaqController;
use App\Http\Controllers\Api\Admin\ContactController;
use App\Http\Controllers\Api\Admin\DynamicContentModuleController;
use App\Http\Controllers\Api\Admin\KnowledgeAdminController;
use App\Http\Controllers\Api\Admin\ChatPromptController;

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('roles', [RolePermissionController::class, 'roles']);
        Route::post('roles', [RolePermissionController::class, 'createRole']);
        Route::post('roles/{role}/permissions/sync', [RolePermissionController::class, 'syncRolePermissions']);

        Route::get('permissions', [RolePermissionController::class, 'permissions']);
        Route::post('permissions', [RolePermissionController::class, 'createPermission']);

        Route::post('users/{user}/roles/assign', [RolePermissionController::class, 'assignRole']);
        Route::post('users/{user}/roles/remove', [RolePermissionController::class, 'removeRole']);
        Route::post('users/{user}/permissions/assign', [RolePermissionController::class, 'assignPermission']);
        Route::post('users/{user}/permissions/remove', [RolePermissionController::class, 'removePermission']);

        Route::apiResource('blogs', BlogController::class);
        Route::apiResource('blogs.translations', BlogTranslationController::class);

        Route::apiResource('services', ServiceController::class);
        Route::apiResource('faqs', FaqController::class);
        Route::apiResource('contact-messages', ContactController::class)->only(['index', 'show', 'destroy']);
        Route::apiResource('modules', DynamicContentModuleController::class);

        Route::apiResource('chat-prompts', ChatPromptController::class);

        Route::post('knowledge/index', [KnowledgeAdminController::class, 'reindex']);
        Route::post('knowledge/reindex', [KnowledgeAdminController::class, 'reindex']);
        Route::get('knowledge/chunks', [KnowledgeAdminController::class, 'chunks']);
    });
