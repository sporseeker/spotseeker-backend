# SpotSeeker Backend API - Development Setup

## 🚀 Quick Start

The SpotSeeker Backend API is now fully configured and ready to use with the RDS database.

### Server Status
- ✅ **Laravel Framework**: Version 10.x
- ✅ **PHP Version**: 8.3 (with MySQL extensions)
- ✅ **Database**: Connected to AWS RDS MySQL
- ✅ **Authentication**: Laravel Passport configured
- ✅ **Migrations**: All database tables created and up-to-date

### Starting the Server

```bash
# Option 1: Use the convenience script
./start-server.sh

# Option 2: Manual command
/usr/bin/php8.3 artisan serve --host=0.0.0.0 --port=8000
```

### API Endpoints

**Base URL**: `http://localhost:8000/api`

**Health Check**: `GET http://localhost:8000/api/healthcheck`

### Testing the API

**Option 1: Swagger UI (Interactive Documentation)**
- **URL**: `http://localhost:8000/api/documentation`
- **Features**: Interactive testing, authentication, real-time responses
- **Perfect for**: API exploration, testing, and debugging

**Option 2: REST Client Extension**
Use the `api_test.http` file with the **REST Client** extension in VS Code:

1. Install the "REST Client" extension in VS Code
2. Open the `api_test.http` file
3. Click on "Send Request" above any endpoint to test it

### Key API Endpoints

#### Public Endpoints
- `GET /api/healthcheck` - API health check
- `GET /api/events?status[]=ongoing&status[]=pending` - Get events
- `GET /api/event/{uid}` - Get event by UID
- `GET /api/search?q={query}` - Search events
- `POST /api/auth/register` - User registration
- `POST /api/login` - User login
- `POST /api/bookings` - Create booking

#### Authentication Required
- `GET /api/user` - Get user profile
- `POST /api/user/profile` - Update user profile
- `GET /api/user/orders` - Get user orders

#### Admin Only Endpoints
- `POST /api/events` - Create event
- `PUT /api/events/{id}` - Update event
- `GET /api/users` - Get all users
- `GET /api/bookings` - Get all bookings

### Database Information

The API is connected to the production AWS RDS database:
- **Host**: `awseb-e-2wxu237hsd-stack-awsebrdsdatabase-8jq9hfivlvtb.cjimk4eeuq9l.ap-south-1.rds.amazonaws.com`
- **Database**: `ebdb`
- **All migrations**: ✅ Applied (67 tables created)

### Environment Configuration

The `.env` file is already configured with:
- RDS database credentials
- AWS S3 configuration for file storage
- Email configuration (Resend)
- SMS configuration (SendLK)
- Payment gateway settings
- Social login configurations (Google, Facebook, Apple)

### Example API Usage

#### Register a new user:
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone_no": "0771234567",
  "verification_method": "sms"
}
```

#### Get events:
```http
GET http://localhost:8000/api/events?status[]=ongoing&status[]=pending
```

#### Search events:
```http
GET http://localhost:8000/api/search?q=concert
```

### Features Available

- ✅ Event management
- ✅ User authentication & authorization
- ✅ Ticket booking system
- ✅ Payment processing
- ✅ Venue management
- ✅ Promotion codes
- ✅ Event invitations
- ✅ Notification system
- ✅ Analytics tracking
- ✅ Manager & coordinator roles
- ✅ Mobile verification
- ✅ Social login integration

### Troubleshooting

If you encounter any issues:

1. **Database connection errors**: The RDS credentials are already configured
2. **PHP version issues**: Make sure to use `/usr/bin/php8.3` instead of `php`
3. **Permission errors**: Run `php artisan passport:keys --force` if needed

### Development Notes

- The API uses Laravel Sanctum for authentication
- File uploads are stored in AWS S3
- Events support multiple ticket packages
- Real-time features use Laravel's broadcasting
- The system supports multi-language content
- Analytics integration with Google Analytics and Facebook Pixel

Happy coding! 🎉
