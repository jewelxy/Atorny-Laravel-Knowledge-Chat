# Public API v1 Documentation

Base path: `/api/v1`

This document describes the public API endpoints available in the backend.

## Blogs

- `GET /api/v1/blogs`
  - Description: Retrieve a paginated list of blog posts.
  - Response: list of blogs with metadata such as title, slug, excerpt, published date.

- `GET /api/v1/blogs/{slug}`
  - Description: Retrieve details for a single blog post by slug.
  - Path parameters:
    - `slug` (string) - Blog post slug identifier.
  - Response: blog post details including title, content, author, published date.

## Pages

- `GET /api/v1/pages/{slug}`
  - Description: Retrieve a CMS page by slug.
  - Path parameters:
    - `slug` (string) - CMS page slug.
  - Response: CMS page details including title, content, and related metadata.

## Services

- `GET /api/v1/services`
  - Description: Retrieve a list of available services.
  - Response: list of service entries with titles, slugs, and summary information.

- `GET /api/v1/services/{slug}`
  - Description: Retrieve a specific service by slug.
  - Path parameters:
    - `slug` (string) - Service slug identifier.
  - Response: service detail including title, description, and related content.

## FAQs

- `GET /api/v1/faqs`
  - Description: Retrieve the list of frequently asked questions.
  - Response: array of FAQ items with questions and answers.

- `GET /api/v1/faqs/{slug}`
  - Description: Retrieve a single FAQ item by slug.
  - Path parameters:
    - `slug` (string) - FAQ slug identifier.
  - Response: FAQ question and answer details.

## Contact

- `GET /api/v1/contact`
  - Description: Retrieve public contact information.
  - Response: contact details such as email, phone, address, and other public contact data.

- `POST /api/v1/contact`
  - Description: Submit a contact form.
  - Body parameters: typically form fields such as name, email, subject, message.
  - Response: success confirmation or validation errors.

## Dynamic Content Modules

- `GET /api/v1/modules/{module}`
  - Description: Retrieve a list of entries for a dynamic content module.
  - Path parameters:
    - `module` (string) - Module identifier.
  - Response: list of module entries.

- `GET /api/v1/modules/{module}/{slug}`
  - Description: Retrieve a specific dynamic content module entry by module name and slug.
  - Path parameters:
    - `module` (string) - Module identifier.
    - `slug` (string) - Entry slug identifier.
  - Response: detailed module entry data.

## Knowledge chat (RAG)

- `POST /api/v1/public/chat`
  - Description: Visitor knowledge chat grounded in published site content (blogs, CMS sections). Returns answer, citations (public URLs/paths), and a not-legal-advice disclaimer.
  - Body: `message` (required), `locale` (`en`|`es`), optional `history` array.
  - See [knowledge-chat-api-v1.md](./knowledge-chat-api-v1.md) for full contract.

## Notes

- All public routes except `POST /chat` and `POST /contact` are cached using the `response.cache` middleware.
- The exact request and response shapes depend on the controller implementations and may include localized data if locale handling is enabled.
