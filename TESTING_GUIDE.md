# 🧪 Quick Testing Guide

## Backend Server Status
✅ **Server Running:** `http://192.168.2.53:8000`
✅ **Database:** 20 profiles created with local image storage
✅ **Test User:** ID 1 (for testing likes/dislikes)

---

## 📚 Swagger API Documentation

**Access:** `http://192.168.2.53:8000/api/documentation`

### Available Endpoints:

#### 1. **Get All People (with pagination)**
```
GET /api/v1/people
Query Params: 
  - page: 1 (optional)
  
Response: Paginated list of 10 people per page
```

**Test Request:**
```bash
curl "http://192.168.2.53:8000/api/v1/people?page=1"
```

---

#### 2. **Get Single Person**
```
GET /api/v1/people/{id}
Path Params:
  - id: person ID (1-20)

Response: Single person's full details
```

**Test Request:**
```bash
curl "http://192.168.2.53:8000/api/v1/people/1"
```

---

#### 3. **Like a Person**
```
POST /api/v1/likes/like

Request Body:
{
  "user_id": 1,
  "person_id": 1
}

Response: 201 Created
{
  "message": "Person liked successfully",
  "like": { ... }
}
```

**Test from Swagger:**
1. Click "Like a person" endpoint
2. Click "Try it out"
3. Enter JSON:
```json
{
  "user_id": 1,
  "person_id": 1
}
```
4. Click "Execute"

---

#### 4. **Dislike a Person**
```
POST /api/v1/likes/dislike

Request Body:
{
  "user_id": 1,
  "person_id": 2
}

Response: 201 Created
```

---

#### 5. **Get User's Liked People**
```
GET /api/v1/likes/liked-people
Query Params:
  - user_id: 1 (required)
  - page: 1 (optional)

Response: Paginated list of liked people with full profile details
```

**Test Request:**
```bash
curl "http://192.168.2.53:8000/api/v1/likes/liked-people?user_id=1&page=1"
```

---

## 🧬 Sample Testing Workflow

1. **Get a list of people:**
   - Visit Swagger UI → GET /api/v1/people → Try it out → Execute
   - Note some person IDs (1, 2, 3, etc.)

2. **Like multiple people:**
   - POST /api/v1/likes/like with person_id=1
   - POST /api/v1/likes/like with person_id=2
   - POST /api/v1/likes/like with person_id=3
   - Each request increments that person's likes_count

3. **View your liked people:**
   - GET /api/v1/likes/liked-people?user_id=1
   - Should return the 3 people you just liked

4. **Dislike someone:**
   - POST /api/v1/likes/dislike with person_id=1
   - Updates their dislikes_count

---

## 🔥 Test Case: Reach 50+ Likes Threshold

To trigger the email notification:

1. Create a script that calls the "like" endpoint 50 times for the same person:
```bash
for i in {1..50}; do
  curl -X POST "http://192.168.2.53:8000/api/v1/likes/like" \
    -H "Content-Type: application/json" \
    -d "{\"user_id\": 1, \"person_id\": 1}"
done
```

2. When person_id=1 reaches 50 likes, a job is dispatched to send an email
3. Email is sent to the admin address configured in Laravel

---

## 📱 Mobile App Integration

### Configuration:
- **API Base URL:** `http://192.168.2.53:8000/api/v1`
- **Environment File:** `.env` in project root
- **Test User ID:** 1

### Features to Test:
1. ✅ Splash screen shows red gradient with heart icon (2 sec auto-transition)
2. ✅ Home screen displays person cards with images
3. ✅ Swipe right → Like (POST /api/v1/likes/like)
4. ✅ Swipe left → Dislike (POST /api/v1/likes/dislike)
5. ✅ Liked tab shows all liked people (GET /api/v1/likes/liked-people)
6. ✅ Images load from `/storage/people/` paths
7. ✅ Like counts update in real-time

---

## 🗄️ Database Structure

### People Table
```sql
id (PK)
name (VARCHAR)
age (INTEGER)
location (VARCHAR)
bio (TEXT)
pictures (JSON) -- Array of image URLs
likes_count (INTEGER)
dislikes_count (INTEGER)
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
deleted_at (TIMESTAMP, soft deletes)
```

### Likes Table
```sql
id (PK)
user_id (FK → users.id)
person_id (FK → people.id)
is_liked (BOOLEAN) -- true for like, false for dislike
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
UNIQUE(user_id, person_id) -- Prevent duplicate votes
```

### Users Table
```sql
id (PK)
name (VARCHAR)
email (VARCHAR)
password (VARCHAR, hashed)
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

---

## 🖼️ Image Storage

**Location:** `c:\NEWPL\backend\storage\app\public\people\`
**Access URL:** `http://192.168.2.53:8000/storage/people/{filename}`
**Format:** JPEG images with unique filenames

### Sample Image Paths:
- `/storage/people/6739fd1234abcd.jpg`
- `/storage/people/6739fd5678efgh.jpg`
- etc.

All 20 profiles have locally stored placeholder images (no external dependencies).

---

## 🐛 Troubleshooting

### API Not Responding
```bash
# Check if server is running
cd c:\NEWPL\backend
php artisan serve --host=192.168.2.53 --port=8000
```

### Database Issues
```bash
# Check if data exists
cd c:\NEWPL\backend
php artisan tinker
App\Models\Person::count() // Should return 20
```

### Swagger Docs Not Loading
```bash
# Regenerate Swagger docs
cd c:\NEWPL\backend
php artisan l5-swagger:generate
```

### Images Not Loading
```bash
# Verify storage link
cd c:\NEWPL\backend
php artisan storage:link
```

---

## 📊 Performance Notes

- Paginated queries return 10 items per page
- API timeout: 10 seconds (mobile app)
- Server: Development server (suitable for testing)
- Database: SQLite (suitable for development)

---

## ✨ Next Steps

1. **Test all endpoints** using Swagger UI
2. **Run mobile app** and test UI integration
3. **Monitor database** for like/dislike interactions
4. **Verify email** notifications when 50+ likes reached
5. **Scale infrastructure** for production deployment

---

**Last Updated:** October 27, 2025
**Status:** Ready for Testing ✅
