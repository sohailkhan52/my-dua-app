# 📚 Task Management API Documentation

## 🌐 Base URL

## 🔑 Authentication
Most endpoints require a Bearer Token obtained after login. Include it in the `Authorization` header:


http://127.0.0.1:8000/api

text

## 🔑 Authentication
Most endpoints require a Bearer Token obtained after login. Include it in the `Authorization` header:
Authorization: Bearer {your_token_here}

text

---

## 1️⃣ Authentication Endpoints

### 🔹 Register a New User

Create a new user account.

**Endpoint:** `POST /register`

**Content-Type:** `x-www-form-urlencoded`

#### Request Body
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | ✅ Yes | Full name of the user |
| `email` | string | ✅ Yes | Valid email address |
| `password` | string | ✅ Yes | Minimum 8 characters |
| `password_confirmation` | string | ✅ Yes | Must match password |

#### Example Request
```http
POST http://127.0.0.1:8000/api/register
Content-Type: application/x-www-form-urlencoded

name=amjid&email=amjid@gmail.com&password=12345678&password_confirmation=12345678
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "amjid",
        "email": "amjid@gmail.com",
        "email_verified_at": null,
        "created_at": "2026-04-08T07:55:51.000000Z",
        "updated_at": "2026-04-08T07:55:51.000000Z"
    },
    "authorization": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "type": "bearer"
    }
}
🔹 Login User
Authenticate an existing user and receive an access token.

Endpoint: POST /login

Content-Type: x-www-form-urlencoded

Request Body
Parameter	Type	Required	Description
email	string	✅ Yes	Registered email address
password	string	✅ Yes	Account password
Example Request
http
POST http://127.0.0.1:8000/api/login
Content-Type: application/x-www-form-urlencoded

email=amjid@gmail.com&password=12345678
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "User logged in successfully",
    "user": {
        "id": 1,
        "name": "amjid",
        "email": "amjid@gmail.com",
        "email_verified_at": null,
        "created_at": "2026-04-08T07:55:51.000000Z",
        "updated_at": "2026-04-08T07:55:51.000000Z"
    },
    "authorization": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "type": "bearer"
    }
}
🔹 Logout User
Invalidate the current user's token.

Endpoint: POST /logout

Content-Type: x-www-form-urlencoded

Headers: Authorization: Bearer {token}

Example Request
http
POST http://127.0.0.1:8000/api/logout
Content-Type: application/x-www-form-urlencoded
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "User logged out successfully"
}
2️⃣ Task Management Endpoints
🔹 Get All Tasks
Retrieve a list of all tasks for the authenticated user.

Endpoint: GET /tasks

Headers: Authorization: Bearer {token}

Example Request
http
GET http://127.0.0.1:8000/api/tasks
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "Tasks Retrieved Successfully",
    "data": [
        {
            "id": 2,
            "title": "test22",
            "description": "here is test description",
            "status": "pending",
            "due_date": "2026-04-13",
            "user_id": 1,
            "created_at": "2026-04-08T07:55:51.000000Z",
            "updated_at": "2026-04-08T08:16:55.000000Z"
        }
    ]
}
🔹 Get Single Task
Retrieve details of a specific task by ID.

Endpoint: GET /tasks/{id}

Headers: Authorization: Bearer {token}

Parameter	Type	Description
id	integer	Task ID
Example Request
http
GET http://127.0.0.1:8000/api/tasks/2
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "Task Retrieved Successfully",
    "data": {
        "id": 2,
        "title": "test22",
        "description": "here is test description",
        "status": "pending",
        "due_date": "2026-04-13",
        "user_id": 1,
        "created_at": "2026-04-08T07:55:51.000000Z",
        "updated_at": "2026-04-08T08:16:55.000000Z"
    }
}
🔹 Create Task
Create an existing task.

Endpoint: POST /tasks

Content-Type: x-www-form-urlencoded

Headers: Authorization: Bearer {token}


title	string	✅ Yes	Task title
description	string	✅ Yes	Task description
Example Request
http
PUT http://127.0.0.1:8000/api/tasks
Content-Type: application/x-www-form-urlencoded
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

title=test22&description=here is test description
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "Task created successfully",
    "data": {
        "id": 2,
        "title": "test22",
        "description": "here is test description",
        "status": "pending",
        "due_date": "2026-04-13",
        "user_id": 1,
        "created_at": "2026-04-08T07:55:51.000000Z",
        "updated_at": "2026-04-08T08:16:55.000000Z"
    }
}
🔹 Update Task
Update an existing task.

Endpoint: PUT /tasks/{id}

Content-Type: x-www-form-urlencoded

Headers: Authorization: Bearer {token}

Parameter	Type	Required	Description
id	integer	✅ Yes	Task ID (in URL)
title	string	✅ Yes	Task title
description	string	✅ Yes	Task description
Example Request
http
PUT http://127.0.0.1:8000/api/tasks/2
Content-Type: application/x-www-form-urlencoded
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

title=test22&description=here is test description
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "Task updated successfully",
    "data": {
        "id": 2,
        "title": "test22",
        "description": "here is test description",
        "status": "pending",
        "due_date": "2026-04-13",
        "user_id": 1,
        "created_at": "2026-04-08T07:55:51.000000Z",
        "updated_at": "2026-04-08T08:16:55.000000Z"
    }
}
🔹 Delete Task
Delete a specific task by ID.

Endpoint: DELETE /tasks/{id}

Headers: Authorization: Bearer {token}

Parameter	Type	Description
id	integer	Task ID
Example Request
http
DELETE http://127.0.0.1:8000/api/tasks/2
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
✅ Success Response (200 OK)
json
{
    "status": true,
    "message": "Task deleted successfully",
    "data": {}
}
📊 Response Status Codes
Status Code	Description
200	✅ Success
401	❌ Unauthorized (Invalid or missing token)
404	❌ Not Found (Task doesn't exist)
422	❌ Validation Error (Invalid input data)
500	❌ Server Error
🧪 Quick Testing with cURL
Register
bash
curl -X POST http://127.0.0.1:8000/api/register \
  -d "name=amjid&email=amjid@gmail.com&password=12345678&password_confirmation=12345678"
Login
bash
curl -X POST http://127.0.0.1:8000/api/login \
  -d "email=amjid@gmail.com&password=12345678"
Get All Tasks
bash
curl -X GET http://127.0.0.1:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
Get Single Task
bash
curl -X GET http://127.0.0.1:8000/api/tasks/2 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
Update Task
bash
curl -X PUT http://127.0.0.1:8000/api/tasks/2 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d "title=Updated Task&description=New description"
Delete Task
bash
curl -X DELETE http://127.0.0.1:8000/api/tasks/2 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
Logout
bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
📝 Notes
🔐 Always include the Bearer token for protected endpoints

📅 Date format follows ISO 8601 (Y-m-d or Y-m-d H:i:s)

⚠️ Task status field can be pending, in_progress, or completed

🔄 All task operations are user-specific (users can only access their own tasks)

📧 Need help? Contact the development team.

Documentation generated on: April 08, 2026

text

---

This complete documentation includes:
- ✅ Your existing Laravel README content preserved at the top
- ✅ Beautifully formatted API documentation below
- ✅ All 7 endpoints (Register, Login, Logout, Get All Tasks, Get Single Task, Update Task, Delete Task)
- ✅ Request/response examples for each endpoint
- ✅ cURL commands for quick testing
- ✅ Status codes reference
- ✅ Helpful notes section

