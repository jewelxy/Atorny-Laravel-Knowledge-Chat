<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogTranslation;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blog = Blog::create([
            'title' => 'Welcome to Our Blog',
            'slug' => 'welcome-to-our-blog',
            'content' => 'This is the main blog content.',
            'status' => true,
            'published_at' => now(),
        ]);

        BlogTranslation::create([
            'blog_id' => $blog->id,
            'locale' => 'en',
            'title' => 'Welcome to Our Blog',
            'slug' => 'welcome-to-our-blog',
            'description' => 'This is the English description of our blog post.',
            'meta_tag' => 'Welcome Blog',
            'meta_description' => 'Learn about our latest blog posts and updates.',
        ]);

        BlogTranslation::create([
            'blog_id' => $blog->id,
            'locale' => 'es',
            'title' => 'Bienvenido a Nuestro Blog',
            'slug' => 'bienvenido-a-nuestro-blog',
            'description' => 'Esta es la descripción en español de nuestro artículo de blog.',
            'meta_tag' => 'Blog de Bienvenida',
            'meta_description' => 'Aprende sobre nuestras últimas publicaciones de blog y actualizaciones.',
        ]);
    }
}
