# API Examples - Tinder Clone

## Base URL
```
http://192.168.2.53:8000/api/v1
```

## Authentication
Currently using Test User ID 1. In production, implement proper authentication (Laravel Sanctum or Passport).

---

## 1. Get All People

**Endpoint:** `GET /api/v1/people`

**Query Parameters:**
- `page` (optional): Page number, default 1
- `per_page` (optional): Items per page, default 10

**Example Request:**
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/people?page=1"
```

**Example Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Donna Garcia",
      "age": 30,
      "location": "Washington, DC",
      "bio": "Love hiking and coffee ☕️🏔️",
      "pictures": [
        "/storage/people/6739fd1234abcd.jpg"
      ],
      "likes_count": 0,
      "dislikes_count": 0,
      "created_at": "2025-10-27T13:15:20.000000Z",
      "updated_at": "2025-10-27T13:15:20.000000Z"
    },
    {
      "id": 2,
      "name": "Anna Williams",
      "age": 25,
      "location": "Dallas, TX",
      "bio": "Artist and musician 🎨🎵",
      "pictures": [
        "/storage/people/6739fd5678efgh.jpg"
      ],
      "likes_count": 0,
      "dislikes_count": 0,
      "created_at": "2025-10-27T13:15:21.000000Z",
      "updated_at": "2025-10-27T13:15:21.000000Z"
    }
  ],
  "links": {
    "first": "http://192.168.2.53:8000/api/v1/people?page=1",
    "last": "http://192.168.2.53:8000/api/v1/people?page=2",
    "next": "http://192.168.2.53:8000/api/v1/people?page=2",
    "prev": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "http://192.168.2.53:8000/api/v1/people",
    "per_page": 10,
    "to": 10,
    "total": 20
  }
}
```

---

## 2. Get Single Person

**Endpoint:** `GET /api/v1/people/{id}`

**Path Parameters:**
- `id` (required): Person ID

**Example Request:**
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/people/1"
```

**Example Response (200 OK):**
```json
{
  "id": 1,
  "name": "Donna Garcia",
  "age": 30,
  "location": "Washington, DC",
  "bio": "Love hiking and coffee ☕️🏔️",
  "pictures": [
    "/storage/people/6739fd1234abcd.jpg"
  ],
  "likes_count": 5,
  "dislikes_count": 2,
  "created_at": "2025-10-27T13:15:20.000000Z",
  "updated_at": "2025-10-27T13:15:45.000000Z"
}
```

**Error Response (404 Not Found):**
```json
{
  "message": "No query results found for model [App\\Models\\Person]."
}
```

---

## 3. Like a Person

**Endpoint:** `POST /api/v1/likes/like`

**Request Body:**
```json
{
  "user_id": 1,
  "person_id": 1
}
```

**Example Request:**
```bash
curl -X POST "http://192.168.2.53:8000/api/v1/likes/like" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 1}'
```

**Example Response (201 Created):**
```json
{
  "message": "Person liked successfully",
  "like": {
    "user_id": 1,
    "person_id": 1,
    "is_liked": true,
    "updated_at": "2025-10-27T13:16:30.000000Z",
    "created_at": "2025-10-27T13:16:30.000000Z",
    "id": 1
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "The user id field is required.",
  "errors": {
    "user_id": [
      "The user id field is required."
    ]
  }
}
```

**Behavior:**
- If first time liking this person: increments `person.likes_count`, dispatches job if count >= 50
- If already liked: updates `is_liked` to true
- If previously disliked: updates `is_liked` to true (no re-increment)

---

## 4. Dislike a Person

**Endpoint:** `POST /api/v1/likes/dislike`

**Request Body:**
```json
{
  "user_id": 1,
  "person_id": 2
}
```

**Example Request:**
```bash
curl -X POST "http://192.168.2.53:8000/api/v1/likes/dislike" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 2}'
```

**Example Response (201 Created):**
```json
{
  "message": "Person disliked successfully",
  "like": {
    "user_id": 1,
    "person_id": 2,
    "is_liked": false,
    "updated_at": "2025-10-27T13:16:45.000000Z",
    "created_at": "2025-10-27T13:16:45.000000Z",
    "id": 2
  }
}
```

**Behavior:**
- If first time disliking this person: increments `person.dislikes_count`
- If already disliked: updates `is_liked` to false
- If previously liked: updates `is_liked` to false (no re-increment)

---

## 5. Get User's Liked People

**Endpoint:** `GET /api/v1/likes/liked-people`

**Query Parameters:**
- `user_id` (required): User ID
- `page` (optional): Page number, default 1

**Example Request:**
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/likes/liked-people?user_id=1&page=1"
```

**Example Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "person_id": 1,
      "is_liked": true,
      "created_at": "2025-10-27T13:16:30.000000Z",
      "updated_at": "2025-10-27T13:16:30.000000Z",
      "person": {
        "id": 1,
        "name": "Donna Garcia",
        "age": 30,
        "location": "Washington, DC",
        "bio": "Love hiking and coffee ☕️🏔️",
        "pictures": [
          "/storage/people/6739fd1234abcd.jpg"
        ],
        "likes_count": 1,
        "dislikes_count": 0,
        "created_at": "2025-10-27T13:15:20.000000Z",
        "updated_at": "2025-10-27T13:16:45.000000Z"
      }
    },
    {
      "id": 3,
      "user_id": 1,
      "person_id": 5,
      "is_liked": true,
      "created_at": "2025-10-27T13:17:00.000000Z",
      "updated_at": "2025-10-27T13:17:00.000000Z",
      "person": {
        "id": 5,
        "name": "Ashley Edwards",
        "age": 37,
        "location": "Atlanta, GA",
        "bio": "Marketing professional. Foodie ✈️🍽️",
        "pictures": [
          "/storage/people/6739fd9999abcd.jpg"
        ],
        "likes_count": 1,
        "dislikes_count": 0,
        "created_at": "2025-10-27T13:15:24.000000Z",
        "updated_at": "2025-10-27T13:17:00.000000Z"
      }
    }
  ],
  "links": {
    "first": "http://192.168.2.53:8000/api/v1/likes/liked-people?page=1",
    "last": "http://192.168.2.53:8000/api/v1/likes/liked-people?page=1",
    "next": null,
    "prev": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://192.168.2.53:8000/api/v1/likes/liked-people",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "The user id field is required.",
  "errors": {
    "user_id": [
      "The user id field is required."
    ]
  }
}
```

---

## Complete Testing Sequence

### Step 1: Get All People
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/people"
```
**Expected:** Returns 10 people (page 1)

### Step 2: Like 3 People
```bash
curl -X POST "http://192.168.2.53:8000/api/v1/likes/like" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 1}'

curl -X POST "http://192.168.2.53:8000/api/v1/likes/like" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 2}'

curl -X POST "http://192.168.2.53:8000/api/v1/likes/like" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 3}'
```
**Expected:** Each returns 201 with like data

### Step 3: View Liked People
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/likes/liked-people?user_id=1"
```
**Expected:** Returns 3 people liked by user 1

### Step 4: Change Mind - Dislike One
```bash
curl -X POST "http://192.168.2.53:8000/api/v1/likes/dislike" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1, "person_id": 2}'
```
**Expected:** 201 with is_liked=false

### Step 5: View Liked People Again
```bash
curl -X GET "http://192.168.2.53:8000/api/v1/likes/liked-people?user_id=1"
```
**Expected:** Returns only 2 people (person 2 removed)

---

## HTTP Status Codes

- `200 OK` - Successful GET request
- `201 Created` - Successful POST request (like/dislike)
- `404 Not Found` - Resource doesn't exist
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

---

## Notes

1. **User ID 1** is the test user created during setup
2. All timestamps are in UTC (ISO 8601 format)
3. Pictures array is stored as JSON but returned as array
4. Soft deletes are in place for people (deleted_at field)
5. Like counts are automatically incremented when first interaction occurs
6. Email notification is sent when likes_count >= 50

---

**API Version:** v1
**Updated:** October 27, 2025
