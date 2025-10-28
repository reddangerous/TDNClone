# Tinder Clone - Quick Start Guide

A modern dating application with React Native frontend and Laravel backend.

## Prerequisites

Before you begin, make sure you have installed:

- PHP 8.2+ https://www.php.net/downloads
- Node.js 18+ https://nodejs.org/
- Git https://git-scm.com/
- Composer https://getcomposer.org/

---

## Quick Setup (5 minutes)

### Step 1: Clone Repository

```bash
git clone git@github.com:reddangerous/TDNClone.git.git
cd TDNClone
```

### Step 2: Setup Backend (Laravel)

```bash
cd backend

# Install dependencies
composer install

# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Migrate database and seed
php artisan migrate --seed
```

### Step 3: Find Your Machine IP

On Windows (PowerShell):
```bash
ipconfig
```
Look for "IPv4 Address" under your network adapter. Example: 192.168.x.x or 10.0.x.x

On macOS/Linux:
```bash
ifconfig
```
Look for inet address.

Let's say your IP is: 192.168.2.53

### Step 4: Update Backend Configuration

Edit backend/.env and update:

```
APP_URL=http://YOUR_IP:8000
```

Example:
```
APP_URL=http://192.168.2.53:8000
```

### Step 5: Start Backend Server

```bash
cd backend
php artisan serve --host=YOUR_IP --port=8000
```

Example:
```bash
php artisan serve --host=192.168.2.53 --port=8000
```

Server running at: http://192.168.2.53:8000

### Step 6: Setup Frontend (React Native)

In a new terminal, from project root:

```bash
cd project

# Install dependencies
npm install

# Update API URL in the code
# Edit: project/services/api.ts
```

### Step 7: Update API URL in Mobile App

Open project/services/api.ts and update:

```typescript
const API_URL = 'http://YOUR_IP:8000/api/v1';
```

Example:
```typescript
const API_URL = 'http://192.168.2.53:8000/api/v1';
```

### Step 8: Run Mobile App

```bash
# From project directory
npx expo start

# Choose:
# - Press 'w' for web (browser)
# - Scan QR code with Expo Go app (iOS/Android)
```

---

## Test the API

### Option 1: Swagger UI (Recommended)

Open in browser:
```
http://YOUR_IP:8000/api/documentation
```

Example:
```
http://192.168.2.53:8000/api/documentation
```

Click any endpoint and click "Try it out" button.

### Option 2: Using cURL

Register a User:
```bash
curl -X POST http://192.168.2.53:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

Login:
```bash
curl -X POST http://192.168.2.53:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Get Profiles:
```bash
curl http://192.168.2.53:8000/api/v1/people
```

---

## App Features

- User registration and login
- Browse profiles (20 pre-seeded)
- Like/Dislike profiles
- View liked profiles
- Swipeable card interface
- Real images for each profile

---

## Database Schema

The app includes:
- Users Table - User accounts with Sanctum tokens
- People Table - 20 profiles with images, age, bio, location
- Likes Table - Track user interactions
- Personal Access Tokens - Authentication tokens

Seeded data includes:
- 20 diverse profiles
- Real profile images
- Mixed ages (22-35)
- Various locations

---

## Default Test Credentials

After seeding, you can use any pre-seeded email and password password123:

```
Email: maria@example.com
Password: password123
```

Or register a new user in the app.

---

## Troubleshooting

### Backend won't start?

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear

# Try again
php artisan serve --host=192.168.2.53 --port=8000
```

### Mobile app can't connect to API?

1. Check your IP is correct: ipconfig (Windows)
2. Ensure backend server is running
3. Update project/services/api.ts with correct IP
4. Check firewall allows port 8000

### Database error?

```bash
# Reset database
php artisan migrate:reset --force
php artisan migrate --seed
```

### Port 8000 already in use?

```bash
# Use different port
php artisan serve --host=192.168.2.53 --port=8080
# Then update mobile app API URL to http://192.168.2.53:8080/api/v1
```

---

## Project Structure

```
TDNClone/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php         # All 8 API endpoints
│   ├── .env                # Configuration (UPDATE IP HERE)
│   └── composer.json
│
├── project/                 # React Native/Expo
│   ├── app/                # Screens
│   ├── components/         # UI Components
│   ├── services/
│   │   └── api.ts         # API client (UPDATE IP HERE)
│   ├── app.json
│   └── package.json
│
└── README.md              # This file
```

---

## API Endpoints

Authentication (No Token Required)
- POST /api/v1/auth/register - Create account
- POST /api/v1/auth/login - Login and get token

Discovery (No Token Required)
- GET /api/v1/people - Get all profiles (paginated)
- GET /api/v1/people/{id} - Get single profile

Protected (Token Required)
- GET /api/v1/auth/me - Get current user
- POST /api/v1/auth/logout - Logout
- POST /api/v1/likes/like - Like a profile
- POST /api/v1/likes/dislike - Dislike a profile
- GET /api/v1/likes/liked-people - Get your likes

---

## Security

- Passwords are hashed with bcrypt
- API uses Sanctum for authentication
- All protected routes require Bearer token
- CORS configured for local development

---

## Common Commands

Backend directory:
```bash
cd backend

# Refresh database with seed
php artisan migrate:refresh --seed

# Check routes
php artisan route:list

# Clear all caches
php artisan cache:clear
php artisan config:clear

# Generate new app key (if needed)
php artisan key:generate
```

Frontend directory:
```bash
cd project

# Install dependencies
npm install

# Start expo
npx expo start
```

---

## Verification Checklist

Before testing, verify:

- PHP 8.2+ installed: php -v
- Composer installed: composer -v
- Node.js 18+ installed: node -v
- Git installed: git -v
- Backend migrations ran: php artisan migrate --seed
- .env file has correct APP_URL
- api.ts has correct API URL
- Backend server running: php artisan serve --host=YOUR_IP --port=8000
- Swagger UI accessible: http://YOUR_IP:8000/api/documentation
- Mobile app running: npx expo start

---

## Quick Test Flow

1. Start Backend:
```bash
cd backend
php artisan serve --host=192.168.2.53 --port=8000
```

2. Test API (in browser):
```
http://192.168.2.53:8000/api/documentation
```

3. Start Frontend:
```bash
cd project
npx expo start
```

4. In App:
- Click Register or use test credentials
- Log in
- Start swiping

---

## Support

- Check .env file configuration
- Check API URL in project/services/api.ts
- Check firewall allows port 8000
- Check IP address is correct with ipconfig

---

Ready to run? Start with Step 1 above!
