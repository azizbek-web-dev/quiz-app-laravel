# Quiz App API Documentation

## Authentication System

Bu API Twilio OTP verification bilan to'liq authentication tizimini taqdim etadi.

### Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. User Registration
**POST** `/auth/register`

Foydalanuvchini ro'yxatdan o'tkazish va OTP yuborish.

#### Request Body
```json
{
    "full_name": "John Doe",
    "username": "johndoe",
    "phone": "+998901234567",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Response (Success - 201)
```json
{
    "success": true,
    "message": "User registered successfully. Please verify your phone number.",
    "data": {
        "user_id": 1,
        "phone": "+998901234567",
        "otp_expires_at": "2024-01-01T12:05:00.000000Z"
    }
}
```

#### Response (Error - 422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "phone": ["The phone field is required."],
        "username": ["The username has already been taken."]
    }
}
```

### 2. Verify OTP
**POST** `/auth/verify-otp`

Telefon raqamini OTP kodi bilan tasdiqlash.

#### Request Body
```json
{
    "phone": "+998901234567",
    "otp_code": "123456"
}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Phone verified successfully",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "username": "johndoe",
            "phone": "+998901234567",
            "phone_verified_at": "2024-01-01T12:05:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 3. Resend OTP
**POST** `/auth/resend-otp`

OTP kodini qayta yuborish.

#### Request Body
```json
{
    "phone": "+998901234567"
}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "OTP sent successfully",
    "data": {
        "otp_expires_at": "2024-01-01T12:10:00.000000Z"
    }
}
```

### 4. User Login
**POST** `/auth/login`

Foydalanuvchini tizimga kirish (username, phone yoki email bilan).

#### Request Body
```json
{
    "login": "johndoe", // username, phone (+998901234567) yoki email
    "password": "password123"
}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "username": "johndoe",
            "phone": "+998901234567",
            "phone_verified_at": "2024-01-01T12:05:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 5. User Logout
**POST** `/auth/logout`

Foydalanuvchini tizimdan chiqarish.

#### Headers
```
Authorization: Bearer {token}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### 6. Get User Profile
**GET** `/auth/profile`

Foydalanuvchi profilini olish.

#### Headers
```
Authorization: Bearer {token}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "username": "johndoe",
            "phone": "+998901234567",
            "phone_verified_at": "2024-01-01T12:05:00.000000Z",
            "created_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

### 7. Update User Profile
**PUT** `/auth/profile`

Foydalanuvchi profilini yangilash.

#### Headers
```
Authorization: Bearer {token}
```

#### Request Body
```json
{
    "full_name": "John Smith",
    "username": "johnsmith"
}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Smith",
            "username": "johnsmith",
            "phone": "+998901234567",
            "phone_verified_at": "2024-01-01T12:05:00.000000Z"
        }
    }
}
```

### 8. Test Authentication
**GET** `/user`

Authentication token'ni tekshirish.

#### Headers
```
Authorization: Bearer {token}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "full_name": "John Doe",
            "username": "johndoe",
            "phone": "+998901234567",
            "phone_verified_at": "2024-01-01T12:05:00.000000Z",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:05:00.000000Z"
        }
    }
}
```

## Validation Rules

### Phone Number
- Format: `+998XXXXXXXXX` (Uzbekistan raqamlari)
- Majburiy maydon
- Unique (takrorlanmas)

### Username
- Majburiy maydon
- Unique (takrorlanmas)
- String, maksimal 255 belgi

### Full Name
- Majburiy maydon
- String, maksimal 255 belgi

### Password
- Majburiy maydon
- Minimum 8 belgi
- Confirmation bilan mos kelishi kerak

## Error Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **500**: Server Error

## Twilio Configuration

Twilio'ni sozlash uchun `.env` faylida quyidagi o'zgaruvchilarni to'ldiring:

```env
TWILIO_SID=your_twilio_account_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=your_twilio_phone_number
```

## Database Schema

### Users Table
- `id` - Primary key
- `full_name` - To'liq ism
- `username` - Foydalanuvchi nomi (unique)
- `phone` - Telefon raqami (unique, +998 format)
- `email` - Email (nullable, unique)
- `password` - Parol (hashed)
- `otp_code` - OTP kodi (nullable)
- `otp_expires_at` - OTP muddati (nullable)
- `phone_verified_at` - Telefon tasdiqlangan vaqt (nullable)
- `email_verified_at` - Email tasdiqlangan vaqt (nullable)
- `remember_token` - Esda qolish tokeni
- `created_at` - Yaratilgan vaqt
- `updated_at` - Yangilangan vaqt

### Personal Access Tokens Table
- `id` - Primary key
- `tokenable_type` - Model turi
- `tokenable_id` - Model ID
- `name` - Token nomi
- `token` - Token (unique)
- `abilities` - Ruxsatlar
- `last_used_at` - Oxirgi foydalanish vaqti
- `expires_at` - Token muddati
- `created_at` - Yaratilgan vaqt
- `updated_at` - Yangilangan vaqt
