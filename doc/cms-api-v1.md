# CMS API v1 Documentation

Headless, section-based CMS. Pages are shells; content lives in **sections** with per-locale **JSONB `data`** documents. Layout and styling are handled in the frontend per `section_type`.

See also: [cms-headless-architecture.md](./cms-headless-architecture.md)

---

## Base URLs

| Audience | Base path | Auth |
|----------|-----------|------|
| **Public** (frontend / mobile) | `/api/v1/public` | None |
| **Admin** (CMS management) | `/api/v1/cms` | Bearer token + `admin` role |

Global middleware on `/api/v1`: `force.json`, `set.locale`

---

## Response envelope

All endpoints return:

```json
{
  "status": true,
  "message": "Human-readable description",
  "data": {}
}
```

Errors:

```json
{
  "status": false,
  "message": "Error description"
}
```

Validation errors (422) follow Laravel’s standard `errors` object when applicable.

---

# Public API

**No authentication.** Responses are cached (1 hour) via `response.cache` middleware. Cache is invalidated automatically when CMS content changes.

## Get published page

`GET /api/v1/public/pages/{key}`

| Parameter | Location | Description |
|-----------|----------|-------------|
| `key` | path | Page identifier (e.g. `home`, `about`) |
| `locale` | query | Optional. `en` or `es` (see `config/localization.php`). Defaults to app locale. |

**Example**

```bash
curl "http://localhost:8000/api/v1/public/pages/home?locale=en"
```

**Response (200)**

```json
{
  "status": true,
  "message": "CMS page loaded",
  "data": {
    "page": "home",
    "locale": "en",
    "sections": [
      {
        "section_key": "hero_main",
        "section_type": "hero",
        "sort_order": 0,
        "data": {
          "headline": "Expert Bankruptcy Guidance You Can Trust",
          "subheadline": "Protect your assets and rebuild your financial future.",
          "cta": {
            "label": "Free Consultation",
            "href": "/contact",
            "variant": "primary"
          },
          "media": {
            "type": "image",
            "src": "/assets/hero-home.jpg",
            "alt": "Attorney meeting with client"
          }
        }
      },
      {
        "section_key": "faq_primary",
        "section_type": "faq",
        "sort_order": 10,
        "data": {
          "title": "Frequently Asked Questions",
          "items": [
            {
              "id": "faq-1",
              "question": "How long does bankruptcy take?",
              "answer": "Chapter 7 typically completes in 3–6 months."
            }
          ]
        }
      }
    ]
  }
}
```

**Notes**

- Only **published** pages (`status: true`) are returned.
- Only **active** sections (`is_active: true`) are included, ordered by `sort_order`.
- If a translation is missing for the requested locale, the **fallback locale** (`en`) is used.
- Public sections do **not** include `settings` or `styles` — the frontend owns presentation.

**Errors**

| Code | When |
|------|------|
| 404 | Page not found or unpublished |

---

# Admin API

**Authentication required**

```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
```

Middleware: `auth:sanctum`, `role:admin`

---

## Pages

### List pages

`GET /api/v1/cms/pages`

Returns all pages with nested `sections` and `translations`.

**Response (200)**

```json
{
  "status": true,
  "message": "CMS pages loaded",
  "data": {
    "pages": [
      {
        "id": 1,
        "key": "home",
        "status": true,
        "sections": [],
        "created_by": null,
        "updated_by": null,
        "created_at": "2026-05-19T10:00:00.000000Z",
        "updated_at": "2026-05-19T10:00:00.000000Z"
      }
    ]
  }
}
```

### Create page

`POST /api/v1/cms/pages`

**Body**

| Field | Type | Rules |
|-------|------|-------|
| `key` | string | Required. Max 255. `alpha_dash`. Unique. |
| `status` | boolean | Optional. Default `true`. |

```json
{
  "key": "home",
  "status": true
}
```

**Response (201)** — `data.page` object (see admin page shape above).

### Get page

`GET /api/v1/cms/pages/{page}`

`{page}` — route model binding by page **ID**.

### Update page

`PUT` or `PATCH` `/api/v1/cms/pages/{page}`

| Field | Type | Rules |
|-------|------|-------|
| `key` | string | Optional. `alpha_dash`. Unique (ignore current). |
| `status` | boolean | Optional. |

### Delete page

`DELETE /api/v1/cms/pages/{page}`

Cascade-deletes all sections and translations.

**Response (200)**

```json
{
  "status": true,
  "message": "CMS page deleted"
}
```

---

## Sections

### List sections

`GET /api/v1/cms/sections`

**Query**

| Parameter | Description |
|-----------|-------------|
| `cms_page_id` | Optional. Filter sections by page ID. |

**Response (200)**

```json
{
  "status": true,
  "message": "CMS sections loaded",
  "data": {
    "sections": [
      {
        "id": 1,
        "cms_page_id": 1,
        "section_key": "hero_main",
        "section_type": "hero",
        "sort_order": 0,
        "is_active": true,
        "translations": [],
        "created_at": "...",
        "updated_at": "..."
      }
    ]
  }
}
```

### Create section

`POST /api/v1/cms/sections`

**Body**

| Field | Type | Rules |
|-------|------|-------|
| `cms_page_id` | integer | Required. Must exist in `cms_pages`. |
| `section_key` | string | Required. Max 255. Unique per page. |
| `section_type` | string | Required. Max 100. Maps to frontend component. |
| `sort_order` | integer | Optional. Min 0. Default `0`. |
| `is_active` | boolean | Optional. Default `true`. |

```json
{
  "cms_page_id": 1,
  "section_key": "hero_main",
  "section_type": "hero",
  "sort_order": 0,
  "is_active": true
}
```

### Get section

`GET /api/v1/cms/sections/{section}`

Includes `translations` when loaded.

### Update section

`PUT` or `PATCH` `/api/v1/cms/sections/{section}`

All body fields optional. Same validation as create (unique `section_key` scoped to page).

### Delete section

`DELETE /api/v1/cms/sections/{section}`

Cascade-deletes translations.

---

## Section translations

All translatable content goes in the **`data`** object (JSON). Do not send `title`, `subtitle`, or other fixed columns — they do not exist.

### List translations

`GET /api/v1/cms/sections/{section}/translations`

**Response (200)**

```json
{
  "status": true,
  "message": "CMS section translations retrieved",
  "data": {
    "cms_section_id": 1,
    "translations": [
      {
        "id": 1,
        "locale": "en",
        "data": { "headline": "..." },
        "created_at": "...",
        "updated_at": "..."
      }
    ]
  }
}
```

### Create or upsert translation

`POST /api/v1/cms/sections/{section}/translations`

Creates or updates by `(section_id, locale)`.

**Body**

| Field | Type | Rules |
|-------|------|-------|
| `locale` | string | Required. Max 5. Must be in `supported_locales` (`en`, `es`). |
| `data` | object | Required. Any nested JSON structure. |

**Hero example**

```json
{
  "locale": "en",
  "data": {
    "headline": "Expert Bankruptcy Guidance You Can Trust",
    "subheadline": "Protect your assets and rebuild your financial future.",
    "cta": {
      "label": "Free Consultation",
      "href": "/contact",
      "variant": "primary"
    },
    "media": {
      "type": "image",
      "src": "/assets/hero-home.jpg",
      "alt": "Attorney meeting with client"
    }
  }
}
```

**FAQ example**

```json
{
  "locale": "en",
  "data": {
    "title": "Frequently Asked Questions",
    "items": [
      {
        "id": "faq-1",
        "question": "How long does bankruptcy take?",
        "answer": "Chapter 7 typically completes in 3–6 months."
      }
    ]
  }
}
```

**Pricing example**

```json
{
  "locale": "en",
  "data": {
    "title": "Transparent Legal Plans",
    "subtitle": "Choose the level of support that fits your situation.",
    "plans": [
      {
        "id": "plan-basic",
        "name": "Essential",
        "price": { "amount": 999, "currency": "USD", "period": "flat" },
        "features": ["Initial consultation", "Document review"],
        "highlighted": false,
        "cta": { "label": "Get Started", "href": "/contact?plan=essential" }
      }
    ]
  }
}
```

**Response (201)**

```json
{
  "status": true,
  "message": "CMS section translation saved",
  "data": {
    "translation": {
      "id": 1,
      "locale": "en",
      "data": { "...": "..." },
      "created_at": "...",
      "updated_at": "..."
    }
  }
}
```

### Get translation

`GET /api/v1/cms/sections/{section}/translations/{translation}`

### Update translation

`PUT` or `PATCH` `/api/v1/cms/sections/{section}/translations/{translation}`

**Body**

| Field | Type | Rules |
|-------|------|-------|
| `data` | object | Required. Replaces entire `data` document. |

### Delete translation

`DELETE /api/v1/cms/sections/{section}/translations/{translation}`

---

## HTTP status codes

| Code | Meaning |
|------|---------|
| 200 | Success (GET, PUT, PATCH, DELETE) |
| 201 | Created (POST) |
| 401 | Missing or invalid token |
| 403 | Not admin |
| 404 | Resource not found |
| 422 | Validation failed |
| 500 | Server error |

---

## End-to-end workflow (curl)

### 1. Create page

```bash
curl -X POST http://localhost:8000/api/v1/cms/pages \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"key": "home", "status": true}'
```

### 2. Create section

```bash
curl -X POST http://localhost:8000/api/v1/cms/sections \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "cms_page_id": 1,
    "section_key": "hero_main",
    "section_type": "hero",
    "sort_order": 0
  }'
```

### 3. Add English translation

```bash
curl -X POST http://localhost:8000/api/v1/cms/sections/1/translations \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "locale": "en",
    "data": {
      "headline": "Welcome",
      "cta": { "label": "Contact Us", "href": "/contact" }
    }
  }'
```

### 4. Fetch public page (frontend)

```bash
curl "http://localhost:8000/api/v1/public/pages/home?locale=en"
```

---

## Cache behaviour

Public GET responses are cached for **3600 seconds** (Redis). Cache keys include a version suffix; the version bumps automatically when any `CmsPage`, `CmsSection`, or `CmsSectionTranslation` is saved or deleted.

To force-refresh manually:

```bash
php artisan cache:clear
```

Or:

```bash
php artisan tinker --execute="app(\App\Services\Cache\PublicResponseCacheService::class)->bump();"
```

---

## Route reference

| Method | URI |
|--------|-----|
| GET | `/api/v1/public/pages/{key}` |
| GET | `/api/v1/cms/pages` |
| POST | `/api/v1/cms/pages` |
| GET | `/api/v1/cms/pages/{page}` |
| PUT/PATCH | `/api/v1/cms/pages/{page}` |
| DELETE | `/api/v1/cms/pages/{page}` |
| GET | `/api/v1/cms/sections` |
| POST | `/api/v1/cms/sections` |
| GET | `/api/v1/cms/sections/{section}` |
| PUT/PATCH | `/api/v1/cms/sections/{section}` |
| DELETE | `/api/v1/cms/sections/{section}` |
| GET | `/api/v1/cms/sections/{section}/translations` |
| POST | `/api/v1/cms/sections/{section}/translations` |
| GET | `/api/v1/cms/sections/{section}/translations/{translation}` |
| PUT/PATCH | `/api/v1/cms/sections/{section}/translations/{translation}` |
| DELETE | `/api/v1/cms/sections/{section}/translations/{translation}` |

---

## Seeding demo data

```bash
php artisan db:seed --class=CmsSectionSeeder
```

Seeds page `home` with `hero_main`, `faq_primary`, and `pricing_plans` sections (English + Spanish).
