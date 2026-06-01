# Admin API v1 Documentation

Base path: `/api/v1/admin`

**Authentication**: All endpoints require Bearer token authentication (`auth:sanctum`) and admin role.

**Headers**: 
```
Authorization: Bearer {token}
Content-Type: application/json
```

---

## Blogs Management

### List All Blogs

- `GET /api/v1/admin/blogs`
  - Description: Retrieve all blog posts.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin blog list loaded",
      "data": {
        "blogs": []
      }
    }
    ```

### Create Blog

- `POST /api/v1/admin/blogs`
  - Description: Create a new blog post.
  - Request body:
    ```json
    {
      "title": "My First Blog Post",
      "slug": "my-first-blog-post",
      "content": "This is the content of my blog post."
    }
    ```
  - Response (201):
    ```json
    {
      "success": true,
      "message": "Blog created",
      "data": {
        "blog": {
          "title": "My First Blog Post",
          "slug": "my-first-blog-post",
          "content": "This is the content of my blog post."
        }
      }
    }
    ```

### Get Single Blog

- `GET /api/v1/admin/blogs/{id}`
  - Description: Retrieve a specific blog post by ID.
  - Path parameters:
    - `id` (integer) - Blog post ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin blog loaded",
      "data": {
        "blog": {
          "id": 1
        }
      }
    }
    ```

### Update Blog

- `PUT /api/v1/admin/blogs/{id}`
  - Description: Update an existing blog post.
  - Path parameters:
    - `id` (integer) - Blog post ID.
  - Request body (all fields optional):
    ```json
    {
      "title": "Updated Blog Title",
      "slug": "updated-blog-slug",
      "content": "Updated blog content."
    }
    ```
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Blog updated",
      "data": {
        "blog": {
          "id": 1,
          "title": "Updated Blog Title",
          "slug": "updated-blog-slug",
          "content": "Updated blog content."
        }
      }
    }
    ```

### Delete Blog

- `DELETE /api/v1/admin/blogs/{id}`
  - Description: Delete a blog post.
  - Path parameters:
    - `id` (integer) - Blog post ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Blog deleted",
      "data": {
        "blog": {
          "id": 1
        }
      }
    }
    ```

---

## Services Management

### List All Services

- `GET /api/v1/admin/services`
  - Description: Retrieve all services.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin services list loaded",
      "data": {
        "services": []
      }
    }
    ```

### Create Service

- `POST /api/v1/admin/services`
  - Description: Create a new service.
  - Request body:
    ```json
    {
      "name": "Web Development",
      "description": "Professional web development services",
      "slug": "web-development"
    }
    ```
  - Response (201):
    ```json
    {
      "success": true,
      "message": "Service created",
      "data": {
        "service": {
          "name": "Web Development",
          "description": "Professional web development services",
          "slug": "web-development"
        }
      }
    }
    ```

### Get Single Service

- `GET /api/v1/admin/services/{id}`
  - Description: Retrieve a specific service by ID.
  - Path parameters:
    - `id` (integer) - Service ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin service loaded",
      "data": {
        "service": {
          "id": 1
        }
      }
    }
    ```

### Update Service

- `PUT /api/v1/admin/services/{id}`
  - Description: Update an existing service.
  - Path parameters:
    - `id` (integer) - Service ID.
  - Request body (all fields optional):
    ```json
    {
      "name": "Updated Service Name",
      "description": "Updated service description",
      "slug": "updated-service-slug"
    }
    ```
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Service updated",
      "data": {
        "service": {
          "id": 1,
          "name": "Updated Service Name",
          "description": "Updated service description",
          "slug": "updated-service-slug"
        }
      }
    }
    ```

### Delete Service

- `DELETE /api/v1/admin/services/{id}`
  - Description: Delete a service.
  - Path parameters:
    - `id` (integer) - Service ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Service deleted",
      "data": {
        "service": {
          "id": 1
        }
      }
    }
    ```

---

## FAQs Management

### List All FAQs

- `GET /api/v1/admin/faqs`
  - Description: Retrieve all frequently asked questions.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin FAQ list loaded",
      "data": {
        "faqs": []
      }
    }
    ```

### Create FAQ

- `POST /api/v1/admin/faqs`
  - Description: Create a new FAQ entry.
  - Request body:
    ```json
    {
      "question": "What is your return policy?",
      "answer": "We offer a 30-day money-back guarantee on all purchases.",
      "slug": "return-policy"
    }
    ```
  - Response (201):
    ```json
    {
      "success": true,
      "message": "FAQ created",
      "data": {
        "faq": {
          "question": "What is your return policy?",
          "answer": "We offer a 30-day money-back guarantee on all purchases.",
          "slug": "return-policy"
        }
      }
    }
    ```

### Get Single FAQ

- `GET /api/v1/admin/faqs/{id}`
  - Description: Retrieve a specific FAQ by ID.
  - Path parameters:
    - `id` (integer) - FAQ ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Admin FAQ loaded",
      "data": {
        "faq": {
          "id": 1
        }
      }
    }
    ```

### Update FAQ

- `PUT /api/v1/admin/faqs/{id}`
  - Description: Update an existing FAQ entry.
  - Path parameters:
    - `id` (integer) - FAQ ID.
  - Request body (all fields optional):
    ```json
    {
      "question": "Updated question?",
      "answer": "Updated answer text.",
      "slug": "updated-slug"
    }
    ```
  - Response (200):
    ```json
    {
      "success": true,
      "message": "FAQ updated",
      "data": {
        "faq": {
          "id": 1,
          "question": "Updated question?",
          "answer": "Updated answer text.",
          "slug": "updated-slug"
        }
      }
    }
    ```

### Delete FAQ

- `DELETE /api/v1/admin/faqs/{id}`
  - Description: Delete a FAQ entry.
  - Path parameters:
    - `id` (integer) - FAQ ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "FAQ deleted",
      "data": {
        "faq": {
          "id": 1
        }
      }
    }
    ```

---

## Contact Messages Management

### List All Contact Messages

- `GET /api/v1/admin/contact-messages`
  - Description: Retrieve all contact form submissions.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Contact submissions loaded",
      "data": {
        "messages": []
      }
    }
    ```

### Get Single Contact Message

- `GET /api/v1/admin/contact-messages/{id}`
  - Description: Retrieve a specific contact message by ID.
  - Path parameters:
    - `id` (integer) - Message ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Contact submission loaded",
      "data": {
        "message": {
          "id": 1
        }
      }
    }
    ```

### Delete Contact Message

- `DELETE /api/v1/admin/contact-messages/{id}`
  - Description: Delete a contact message.
  - Path parameters:
    - `id` (integer) - Message ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Contact submission deleted",
      "data": {
        "message": {
          "id": 1
        }
      }
    }
    ```

---

## Dynamic Content Modules Management

### List All Modules

- `GET /api/v1/admin/modules`
  - Description: Retrieve all dynamic content modules.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Dynamic content modules loaded",
      "data": {
        "modules": []
      }
    }
    ```

### Create Module

- `POST /api/v1/admin/modules`
  - Description: Create a new dynamic content module.
  - Request body:
    ```json
    {
      "module_name": "testimonials",
      "schema": {
        "fields": [
          {
            "name": "author",
            "type": "string"
          },
          {
            "name": "content",
            "type": "text"
          }
        ]
      }
    }
    ```
  - Response (201):
    ```json
    {
      "success": true,
      "message": "Dynamic module created",
      "data": {
        "module": {
          "module_name": "testimonials",
          "schema": {
            "fields": [
              {
                "name": "author",
                "type": "string"
              },
              {
                "name": "content",
                "type": "text"
              }
            ]
          }
        }
      }
    }
    ```

### Get Single Module

- `GET /api/v1/admin/modules/{id}`
  - Description: Retrieve a specific module by ID.
  - Path parameters:
    - `id` (integer) - Module ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Dynamic module loaded",
      "data": {
        "module": {
          "id": 1
        }
      }
    }
    ```

### Update Module

- `PUT /api/v1/admin/modules/{id}`
  - Description: Update an existing module.
  - Path parameters:
    - `id` (integer) - Module ID.
  - Request body (all fields optional):
    ```json
    {
      "module_name": "updated_module",
      "schema": {
        "fields": [
          {
            "name": "updated_field",
            "type": "string"
          }
        ]
      }
    }
    ```
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Dynamic module updated",
      "data": {
        "module": {
          "id": 1,
          "module_name": "updated_module",
          "schema": {
            "fields": [
              {
                "name": "updated_field",
                "type": "string"
              }
            ]
          }
        }
      }
    }
    ```

### Delete Module

- `DELETE /api/v1/admin/modules/{id}`
  - Description: Delete a module.
  - Path parameters:
    - `id` (integer) - Module ID.
  - Response (200):
    ```json
    {
      "success": true,
      "message": "Dynamic module deleted",
      "data": {
        "module": {
          "id": 1
        }
      }
    }
    ```

---

## Common Response Structure

All API responses follow this format:

```json
{
  "success": true,
  "message": "Operation description",
  "data": {}
}
```

- **success** (boolean): Indicates whether the request was successful.
- **message** (string): Description of the operation result.
- **data** (object): Response payload containing the requested data.

## HTTP Status Codes

- **200 OK**: Successful GET, PUT, DELETE operation.
- **201 Created**: Successful POST operation (resource created).
- **400 Bad Request**: Invalid request parameters or validation errors.
- **401 Unauthorized**: Missing or invalid authentication token.
- **403 Forbidden**: Insufficient permissions (user lacks admin role).
- **404 Not Found**: Requested resource not found.
- **422 Unprocessable Entity**: Validation failed (e.g., duplicate slug).
- **500 Internal Server Error**: Server error.

## Validation Rules

### Blogs
- `title`: Required, string, max 255 characters
- `slug`: Required, string, max 255 characters, unique
- `content`: Required, string

### Services
- `name`: Required, string, max 255 characters
- `description`: Required, string
- `slug`: Required, string, max 255 characters, unique

### FAQs
- `question`: Required, string, max 255 characters
- `answer`: Required, string
- `slug`: Required, string, max 255 characters, unique

### Dynamic Content Modules
- `module_name`: Required, string, max 255 characters
- `schema`: Optional, array (complex schema definition)
