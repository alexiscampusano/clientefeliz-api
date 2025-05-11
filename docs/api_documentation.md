# API Documentation

## Overview
The Cliente Feliz API is a RESTful service that provides endpoints for managing job offers, applications, and user profiles. This documentation provides detailed information about available endpoints, request/response formats, and authentication requirements.

## Base URL
```
http://localhost:8080/api/v1
```

## Authentication
All endpoints except `/auth/login` and `/auth/register` require a valid JWT token in the Authorization header:
```
Authorization: Bearer <your_jwt_token>
```

## API Endpoints

### Authentication

#### Login
```http
POST /auth/login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}
```

#### Register
```http
POST /auth/register
Content-Type: application/json

{
    "email": "string",
    "password": "string",
    "role": "string"
}
```

### Job Offers

#### List Job Offers
```http
GET /job-offers
```

#### Get Job Offer by ID
```http
GET /job-offers/{id}
```

#### Create Job Offer
```http
POST /job-offers
Content-Type: application/json
Authorization: Bearer <token>

{
    "title": "string",
    "description": "string",
    "location": "string",
    "salary": number,
    "contract_type": "string"
}
```

### Applications

#### Submit Application
```http
POST /applications
Content-Type: application/json
Authorization: Bearer <token>

{
    "job_offer_id": number,
    "comment": "string"
}
```

#### Get Application Status
```http
GET /applications/{id}
Authorization: Bearer <token>
```

## Response Formats

### Success Response
```json
{
    "status": "success",
    "message": "string",
    "data": {}
}
```

### Error Response
```json
{
    "status": "error",
    "message": "string",
    "errors": {}
}
```

## Rate Limiting
- 100 requests per minute per IP
- 1000 requests per hour per user

## Versioning
The API uses URL versioning. The current version is v1, accessed through `/api/v1/`.

## Error Codes
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Internal Server Error

## Data Types
- All dates are in ISO 8601 format
- All monetary values are in CLP (Chilean Pesos)
- All IDs are integers

## Best Practices
1. Always include the Content-Type header
2. Handle rate limiting by implementing exponential backoff
3. Cache responses when appropriate
4. Implement proper error handling
5. Use HTTPS in production

## Support
For API support or to report issues, please contact:
- Email: support@clientefeliz.com
- Documentation: https://docs.clientefeliz.com 