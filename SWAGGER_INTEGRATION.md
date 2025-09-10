````markdown
# SpotSeeker API Documentation

## 🚀 Swagger Integration Complete!

The SpotSeeker API now has comprehensive Swagger/OpenAPI documentation integrated.

## 📋 Access Points

### 🌐 Interactive Documentation (Swagger UI)
```
http://localhost:8000/api/documentation
```
**Features:**
- Interactive API testing interface
- Try out endpoints directly from the browser
- Authentication support with Bearer tokens
- Real-time API responses
- Download API specifications

### 📄 API JSON Schema
```
http://localhost:8000/docs
```

## 🔧 Setup & Troubleshooting

### Asset Configuration (Important for GitHub Codespaces)
If Swagger UI shows a white screen, run these commands:

```bash
# Copy Swagger UI assets to the correct location
mkdir -p public/vendor/swagger-api/swagger-ui
cp -r vendor/swagger-api/swagger-ui/dist/* public/vendor/swagger-api/swagger-ui/

# Clear cache and regenerate docs
php artisan config:clear
php artisan l5-swagger:generate
```

### Verify Assets Are Working
```bash
curl -I http://localhost:8000/docs/asset/swagger-ui.css
# Should return HTTP/1.1 200 OK
```

## 📚 What's Documented

### ✅ Implemented Endpoints

#### Authentication
- `POST /api/login` - User login with email/password
- `POST /api/auth/register` - User registration with phone verification
- `POST /api/socialLogin` - Social authentication
- `POST /api/auth/password/forgot` - Password reset request
- `POST /api/auth/manager/login` - Manager login

#### Events
- `GET /api/events` - Get events with filtering (status, featured, etc.)
- `POST /api/events` - Create new event (Admin only)
- `GET /api/event/{uid}` - Get specific event by UID
- `GET /api/search` - Search events by query

#### Bookings
- `POST /api/bookings` - Create new booking
- `POST /api/bookings/update` - Update existing booking
- `POST /api/bookings/{id}` - Get booking details

#### Users
- `GET /api/users` - Get all users (Admin only)
- `GET /api/user` - Get current user profile
- `GET /api/user/orders` - Get user's booking history

#### Venues
- `GET /api/venues` - Get all venues (Admin only)
- `POST /api/venues` - Create new venue (Admin only)

#### Promotions
- `POST /api/promotions/check` - Validate promotion codes

#### General
- `GET /api/healthcheck` - API health check

### 📋 Schema Definitions
- **Event** - Complete event object with venue and ticket packages
- **User** - User profile with roles and status
- **Booking** - Booking details with payment information
- **Venue** - Venue information with location
- **TicketPackage** - Ticket package with pricing
- **ApiResponse** - Standard API response format
- **Validation Errors** - Error response schemas

## 🔐 Authentication

The API uses **Bearer Token Authentication**. In Swagger UI:

1. Click the **"Authorize"** button (🔒)
2. Enter: `Bearer YOUR_TOKEN_HERE`
3. All authenticated endpoints will include the token

## 🎯 Key Features

### Request/Response Examples
Each endpoint includes:
- ✅ Complete request body schemas
- ✅ Response examples with status codes
- ✅ Error response formats
- ✅ Parameter descriptions
- ✅ Enum values for status fields

### Interactive Testing
- ✅ Execute API calls directly from the documentation
- ✅ Real-time responses with actual data
- ✅ Authentication token integration
- ✅ Parameter validation

### Code Generation Support
The OpenAPI specification supports:
- Client SDK generation
- Server stub generation
- API testing tool integration
- Postman collection import

## 🛠️ Development Notes

### Adding New Endpoints
To document new endpoints, add OpenAPI annotations to controller methods:

```php
/**
 * @OA\Post(
 *     path="/api/your-endpoint",
 *     summary="Your Endpoint",
 *     description="Endpoint description",
 *     tags={"YourTag"},
 *     @OA\RequestBody(...),
 *     @OA\Response(...)
 * )
 */
public function yourMethod() { ... }
```

### Regenerating Documentation
```bash
php artisan l5-swagger:generate
```

### Configuration
Swagger settings are in `/config/l5-swagger.php`

## � Next Steps

1. **Open Swagger UI**: http://localhost:8000/api/documentation
2. **Test endpoints** using the interactive interface
3. **Generate client SDKs** from the OpenAPI spec
4. **Import into Postman** or other API testing tools

The documentation is automatically updated when you regenerate with `php artisan l5-swagger:generate` after adding new annotations.

Happy API testing! 🎉

````

### ✅ Implemented Endpoints

#### Authentication
- `POST /api/login` - User login with email/password
- `POST /api/auth/register` - User registration with phone verification
- `POST /api/socialLogin` - Social authentication
- `POST /api/auth/password/forgot` - Password reset request
- `POST /api/auth/manager/login` - Manager login

#### Events
- `GET /api/events` - Get events with filtering (status, featured, etc.)
- `POST /api/events` - Create new event (Admin only)
- `GET /api/event/{uid}` - Get specific event by UID
- `GET /api/search` - Search events by query

#### Bookings
- `POST /api/bookings` - Create new booking
- `POST /api/bookings/update` - Update existing booking
- `POST /api/bookings/{id}` - Get booking details

#### Users
- `GET /api/users` - Get all users (Admin only)
- `GET /api/user` - Get current user profile
- `GET /api/user/orders` - Get user's booking history

#### Venues
- `GET /api/venues` - Get all venues (Admin only)
- `POST /api/venues` - Create new venue (Admin only)

#### Promotions
- `POST /api/promotions/check` - Validate promotion codes

#### General
- `GET /api/healthcheck` - API health check

### 📋 Schema Definitions
- **Event** - Complete event object with venue and ticket packages
- **User** - User profile with roles and status
- **Booking** - Booking details with payment information
- **Venue** - Venue information with location
- **TicketPackage** - Ticket package with pricing
- **ApiResponse** - Standard API response format
- **Validation Errors** - Error response schemas

## 🔐 Authentication

The API uses **Bearer Token Authentication**. In Swagger UI:

1. Click the **"Authorize"** button (🔒)
2. Enter: `Bearer YOUR_TOKEN_HERE`
3. All authenticated endpoints will include the token

## 🎯 Key Features

### Request/Response Examples
Each endpoint includes:
- ✅ Complete request body schemas
- ✅ Response examples with status codes
- ✅ Error response formats
- ✅ Parameter descriptions
- ✅ Enum values for status fields

### Interactive Testing
- ✅ Execute API calls directly from the documentation
- ✅ Real-time responses with actual data
- ✅ Authentication token integration
- ✅ Parameter validation

### Code Generation Support
The OpenAPI specification supports:
- Client SDK generation
- Server stub generation
- API testing tool integration
- Postman collection import

## 🛠️ Development Notes

### Adding New Endpoints
To document new endpoints, add OpenAPI annotations to controller methods:

```php
/**
 * @OA\Post(
 *     path="/api/your-endpoint",
 *     summary="Your Endpoint",
 *     description="Endpoint description",
 *     tags={"YourTag"},
 *     @OA\RequestBody(...),
 *     @OA\Response(...)
 * )
 */
public function yourMethod() { ... }
```

### Regenerating Documentation
```bash
php artisan l5-swagger:generate
```

### Configuration
Swagger settings are in `/config/l5-swagger.php`

## 🚀 Next Steps

1. **Open Swagger UI**: http://localhost:8000/api/documentation
2. **Test endpoints** using the interactive interface
3. **Generate client SDKs** from the OpenAPI spec
4. **Import into Postman** or other API testing tools

The documentation is automatically updated when you regenerate with `php artisan l5-swagger:generate` after adding new annotations.

Happy API testing! 🎉
