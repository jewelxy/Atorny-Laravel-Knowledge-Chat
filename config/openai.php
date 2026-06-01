<?php

return [

    'api_key' => env('OPENAI_ROUTER_API_KEY'),

    'base_url' => env('OPENAI_BASE_URL', 'https://openrouter.ai/api/v1'),

    'chat_model' => env('OPENAI_CHAT_MODEL', 'openai/gpt-4o-mini'),

    'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'openai/text-embedding-3-small'),

    'embedding_driver' => env('OPENAI_EMBEDDING_DRIVER', 'openrouter'),

    'frontend_public_url' => rtrim(env('FRONTEND_PUBLIC_URL', env('APP_URL', 'http://localhost')), '/'),

    'retrieval' => [
        'top_k' => (int) env('KNOWLEDGE_RETRIEVAL_TOP_K', 6),
        'min_similarity' => (float) env('KNOWLEDGE_MIN_SIMILARITY', 0.2),
    ],

    'chunking' => [
        'max_chars' => (int) env('KNOWLEDGE_CHUNK_MAX_CHARS', 1200),
        'overlap_chars' => (int) env('KNOWLEDGE_CHUNK_OVERLAP_CHARS', 200),
    ],

    'disclaimer' => [
        'en' => 'This assistant provides general information only based on this website\'s content. It is not legal advice, does not create an attorney-client relationship, and you should consult a qualified bankruptcy attorney for advice about your situation.',
        'es' => 'Este asistente ofrece información general basada únicamente en el contenido de este sitio. No constituye asesoramiento legal, no crea una relación abogado-cliente y debe consultar a un abogado calificado en bancarrota para obtener orientación sobre su situación.',
    ],

];
