<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\ChatPrompt;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;
use App\Observers\ClearsPublicResponseCacheObserver;
use App\Observers\ReindexesKnowledgeBaseObserver;
use App\Repositories\Contracts\RolePermissionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\RolePermissionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RolePermissionRepositoryInterface::class, RolePermissionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $cacheObserver = ClearsPublicResponseCacheObserver::class;
        $knowledgeObserver = ReindexesKnowledgeBaseObserver::class;

        CmsPage::observe($cacheObserver);
        CmsSection::observe($cacheObserver);
        CmsSectionTranslation::observe($cacheObserver);

        Blog::observe($knowledgeObserver);
        BlogTranslation::observe($knowledgeObserver);
        ChatPrompt::observe($knowledgeObserver);
        CmsPage::observe($knowledgeObserver);
        CmsSection::observe($knowledgeObserver);
        CmsSectionTranslation::observe($knowledgeObserver);
    }
}
