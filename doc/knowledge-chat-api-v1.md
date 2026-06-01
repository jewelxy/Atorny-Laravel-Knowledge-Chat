# Knowledge Chat API (RAG) v1

Visitors receive answers **grounded in indexed content**: published website material (blogs, CMS sections) plus **admin-only chat prompts** (Q&A pairs that never appear on the public site or FAQ pages). The pipeline is:

1. **Retrieve** — embed the question, cosine-similarity search on locale-filtered chunks  
2. **Augment** — inject top chunks into the system prompt  
3. **Generate** — OpenRouter chat completion (`OPENAI_CHAT_MODEL`)

Embeddings use OpenRouter (`OPENAI_EMBEDDING_DRIVER=openrouter`) with `OPENAI_ROUTER_API_KEY`.

## Disclaimer

Every chat response includes a locale-specific **informational-only / not legal advice** disclaimer in the `disclaimer` field. The model is instructed never to provide legal advice.

## Permission layer (not personality)

`ChatAccessPolicy` controls **retrieval scope** and **tools** — not tone:

| Permission | Purpose |
|------------|---------|
| *(none — public)* | Visitors may chat and retrieve public published chunks |
| `use knowledge chat` | Authenticated users explicitly allowed to use chat |
| `reindex knowledge base` | Admin: rebuild chunks + embeddings |
| `view knowledge chunks` | Admin: inspect indexed chunks |
| `retrieve internal knowledge` | Future: include non-public corpora |
| `manage chat prompts` | Admin: CRUD chat-only Q&A (not on website) |
| `submit contact messages` | Enables `contact.submit` tool when wired |

## Admin-only chat prompts (not on website)

Use these when you want the chatbot to answer specific questions **without** adding them to the public FAQ page, CMS FAQ section, or any visitor-facing route.

- Stored in `chat_prompts` — **no** public list/show API
- Indexed as `source_type: chat_prompt` after save (queued reindex) or `POST /api/v1/admin/knowledge/index`
- Matched by **semantic similarity** (same RAG as site content)
- Citations use `chat_only: true` and omit `url` / `path`

### List chat prompts

`GET /api/v1/admin/chat-prompts?locale=en&is_active=true`  
**Permission:** `manage chat prompts`

### Create chat prompt

`POST /api/v1/admin/chat-prompts`

#### Postman

| Field | Value |
|-------|--------|
| Method | `POST` |
| URL | `{{base_url}}/api/v1/admin/chat-prompts` |
| Auth | Bearer Token → `{{admin_token}}` |
| Body | raw → JSON (below) |

```json
{
  "locale": "en",
  "specialist": "marco",
  "question": "What's your name?",
  "answer": "I'm Marco, a David assistant of Bankruptcy Custom Solution. To get us started, which of these best describes your situation? Credit Card Debt, Personal Loans, Lawsuits, Foreclosure, Business Debt, Student Loans, or Other?",
  "is_active": true,
  "sort_order": 0
}
```

| Field | Required | Description |
|-------|----------|-------------|
| `locale` | yes | `en` or `es` |
| `specialist` | no | `marco`, `ruby`, or `alina` — scopes prompt to that persona |
| `question` | yes | Example phrasing visitors might use (3–500 chars) |
| `answer` | yes | Reply the chatbot should ground on (3–8000 chars) |
| `is_active` | no | Default `true`; inactive prompts are skipped when indexing |
| `sort_order` | no | Default `0` |

#### Success response (201)

```json
{
  "status": true,
  "message": "Chat prompt created",
  "data": {
    "prompt": {
      "id": 1,
      "locale": "en",
      "question": "How much does a consultation cost?",
      "answer": "We offer a free initial consultation...",
      "is_active": true,
      "sort_order": 0
    }
  }
}
```

After create/update/delete, run **`POST /api/v1/admin/knowledge/index`** (or wait for the queue worker) so embeddings include the new Q&A.

### Update chat prompt

`PUT /api/v1/admin/chat-prompts/{id}` — same JSON fields as create (`sometimes` rules).

### Delete chat prompt

`DELETE /api/v1/admin/chat-prompts/{id}`

### Get one

`GET /api/v1/admin/chat-prompts/{id}`

## Live chat specialists (Marco / Ruby / Alina)

The frontend assigns each visitor session a specialist. Pass `specialist` on every chat request so name/intro answers match that persona.

| `specialist` | Chatbot name | Role |
|--------------|--------------|------|
| `marco` | Marco | David assistant of Bankruptcy Custom Solution |
| `ruby` | Ruby | David assistant of Bankruptcy Custom Solution |
| `alina` | Alina | David assistant of Bankruptcy Custom Solution |

Default if omitted: `marco` (`CHAT_DEFAULT_SPECIALIST` in `.env`).

**Seeded intro (chat-only, not on website)** — when the visitor asks e.g. *“What’s your name?”*, the matching specialist responds with:

> I'm Marco, a David assistant of Bankruptcy Custom Solution. To get us started, which of these best describes your situation? Credit Card Debt, Personal Loans, Lawsuits, Foreclosure, Business Debt, Student Loans, or Other?

(Ruby and Alina use the same script with their own name; Spanish locale uses the `es` variant.)

Run once after deploy:

```bash
php artisan db:seed --class=ChatPromptSeeder
php artisan knowledge:index
```

---

## Public — visitor chat

`POST /api/v1/public/chat`

**Throttle:** 20 requests / minute per IP

### Body

```json
{
  "message": "What's your name?",
  "locale": "en",
  "specialist": "ruby",
  "history": [
    { "role": "user", "content": "Hello" },
    { "role": "assistant", "content": "Hi! How can I help?" }
  ]
}
```

- `locale` — `en` or `es` (defaults to `Accept-Language` / app locale)
- `specialist` — `marco`, `ruby`, or `alina` (which live chat persona is active)
- `history` — optional, max 12 turns

### Response

```json
{
  "status": true,
  "message": "Knowledge chat response generated",
  "data": {
    "answer": "...",
    "locale": "en",
    "specialist": {
      "key": "ruby",
      "display_name": "Ruby",
      "title": "David assistant",
      "firm": "David assistant of Bankruptcy Custom Solution"
    },
    "disclaimer": "This assistant provides general information only...",
    "citations": [
      {
        "title": "home / faq_primary",
        "slug": "faq_primary",
        "source_type": "cms_section",
        "locale": "en",
        "url": "https://example.com/pages/home#faq_primary",
        "path": "/pages/home#faq_primary",
        "relevance": 0.8421
      }
    ],
    "retrieval": {
      "chunks_used": 4,
      "allowed_tools": []
    },
    "grounded": true
  }
}
```

Citations point at **public paths/URLs** (`FRONTEND_PUBLIC_URL` + slug/path).

## Admin

Requires `auth:sanctum` + `admin` role.

**Headers (all admin knowledge routes):**

```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

---

### Index knowledge base

`POST /api/v1/admin/knowledge/index`  
Alias: `POST /api/v1/admin/knowledge/reindex` (same handler)

**Permission:** `reindex knowledge base`

Rebuilds all chunks and embeddings from published blogs and active CMS sections (`en` / `es` translations). No request body is required.

#### Postman

| Field | Value |
|-------|--------|
| Method | `POST` |
| URL | `{{base_url}}/api/v1/admin/knowledge/index` |
| Auth | Type: **Bearer Token** → Token: `{{admin_token}}` |
| Headers | `Content-Type: application/json`, `Accept: application/json` |
| Body | **none** (leave empty or use raw JSON `{}`) |

**Body tab (optional):** Raw → JSON

```json
{}
```

#### cURL

```bash
curl -X POST "{{base_url}}/api/v1/admin/knowledge/index" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

#### Success response (200)

```json
{
  "status": true,
  "message": "Knowledge base reindexed",
  "data": {
    "indexed": 9,
    "sources": {
      "blog": 2,
      "cms_section": 7,
      "chat_prompt": 3
    }
  }
}
```

#### Error responses

| Status | When |
|--------|------|
| `401` | Missing or invalid Bearer token |
| `403` | Not admin role or missing `reindex knowledge base` permission |
| `503` | OpenRouter/embedding failure (see `message`) |

---

### List chunks

`GET /api/v1/admin/knowledge/chunks?locale=en&source_type=blog&per_page=25`  
**Permission:** `view knowledge chunks`

#### Postman

| Field | Value |
|-------|--------|
| Method | `GET` |
| URL | `{{base_url}}/api/v1/admin/knowledge/chunks` |
| Auth | Bearer Token → `{{admin_token}}` |
| Params | `locale` (optional): `en` \| `es` |
| Params | `source_type` (optional): `blog` \| `cms_section` |
| Params | `per_page` (optional): default `25` |
| Body | **none** |

#### Success response (200)

```json
{
  "status": true,
  "message": "Knowledge chunks loaded",
  "data": {
    "chunks": [
      {
        "id": 1,
        "locale": "en",
        "source_type": "cms_section",
        "source_id": "3:en",
        "title": "home / faq_primary",
        "slug": "faq_primary",
        "path": "/pages/home#faq_primary",
        "url": "https://www.yourfirm.com/pages/home#faq_primary",
        "chunk_index": 0,
        "content_preview": "Chapter 7 typically completes in 3–6 months...",
        "is_public": true
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 25,
      "total": 9
    }
  }
}
```

---

## CLI indexing

```bash
php artisan knowledge:index
```

Same operation as `POST /api/v1/admin/knowledge/index`. Also queued when blogs or CMS content changes (`ReindexKnowledgeBaseJob`).

## Environment

```env
OPENAI_ROUTER_API_KEY=
OPENAI_CHAT_MODEL=openai/gpt-4o-mini
OPENAI_EMBEDDING_MODEL=openai/text-embedding-3-small
OPENAI_EMBEDDING_DRIVER=openrouter
OPENAI_BASE_URL=https://openrouter.ai/api/v1
FRONTEND_PUBLIC_URL=https://www.yourfirm.com
```

Run `php artisan db:seed --class=RolePermissionSeeder` after deploy to register new permissions.
