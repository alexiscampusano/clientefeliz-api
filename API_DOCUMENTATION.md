# Cliente Feliz API Documentation

## Overview

Cliente Feliz API is a RESTful service that facilitates job recruitment processes. It allows recruiters to post job offers and candidates to apply for these positions, providing a streamlined platform for job matching.

## API Architecture & SOLID Principles

The API follows a structured architecture that adheres to SOLID principles:

### 1. Single Responsibility Principle (SRP)
Each component in the system has a single responsibility:
- **Controllers**: Handle HTTP requests and responses
- **Models**: Manage data operations and business logic
- **Services**: Implement specific business functionalities
- **Utils**: Provide utility functions (validation, response handling, etc.)

### 2. Open/Closed Principle (OCP)
The API is designed to be open for extension but closed for modification:
- Base classes (like `BaseModel`) provide core functionality
- Specific implementations extend these base classes without modifying them

### 3. Liskov Substitution Principle (LSP)
Child classes can be used wherever their parent classes are expected:
- All model implementations can be used interchangeably where the base model is expected

### 4. Interface Segregation Principle (ISP)
The API prevents client classes from depending on methods they don't use:
- Controllers expose only the required methods for specific operations
- Models implement only the methods they need

### 5. Dependency Inversion Principle (DIP)
High-level modules don't depend on low-level modules; both depend on abstractions:
- Controllers don't directly interact with data storage
- Models abstract database operations
- Services implement business logic independent of controllers

## Authentication

The API uses JWT (JSON Web Tokens) for authentication. 

### Endpoints

#### Register
- **URL**: `/api/v1/auth/register`
- **Method**: `POST`
- **Authentication**: None
- **Request Body**:
  ```json
  {
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "password": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "user": {
        "id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "role": "string"
      },
      "token": "string"
    },
    "message": null
  }
  ```

#### Login
- **URL**: `/api/v1/auth/login`
- **Method**: `POST`
- **Authentication**: None
- **Request Body**:
  ```json
  {
    "email": "string",
    "password": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "user": {
        "id": "integer",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "role": "string"
      },
      "token": "string"
    },
    "message": null
  }
  ```

#### Current User
- **URL**: `/api/v1/auth/user`
- **Method**: `GET`
- **Authentication**: JWT
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "id": "integer",
      "first_name": "string",
      "last_name": "string",
      "email": "string",
      "role": "string",
      "registration_date": "string"
    },
    "message": null
  }
  ```

#### Logout
- **URL**: `/api/v1/auth/logout`
- **Method**: `POST`
- **Authentication**: JWT
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "success": true,
      "message": "Logged out successfully"
    },
    "message": null
  }
  ```

## Job Offers

### Public Endpoints

#### Get Active Job Offers
- **URL**: `/api/v1/job-offers`
- **Method**: `GET`
- **Authentication**: None
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "job_offers": [
        {
          "id": "integer",
          "title": "string",
          "description": "string",
          "location": "string",
          "salary": "float",
          "contract_type": "string",
          "publication_date": "string",
          "closing_date": "string",
          "status": "string",
          "recruiter_id": "integer"
        }
      ]
    },
    "message": null
  }
  ```

#### Get Job Offer by ID
- **URL**: `/api/v1/job-offers/{id}`
- **Method**: `GET`
- **Authentication**: None
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "job_offer": {
        "id": "integer",
        "title": "string",
        "description": "string",
        "location": "string",
        "salary": "float",
        "contract_type": "string",
        "publication_date": "string",
        "closing_date": "string",
        "status": "string",
        "recruiter_id": "integer",
        "recruiter": {
          "first_name": "string",
          "last_name": "string"
        }
      }
    },
    "message": null
  }
  ```

### Recruiter Endpoints

#### Create Job Offer
- **URL**: `/api/v1/job-offers`
- **Method**: `POST`
- **Authentication**: JWT (Recruiter role)
- **Request Body**:
  ```json
  {
    "title": "string",
    "description": "string",
    "location": "string",
    "salary": "float",
    "contract_type": "string",
    "requirements": "string",
    "benefits": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "job_offer": {
        "id": "integer",
        "title": "string",
        "description": "string",
        "location": "string",
        "salary": "float",
        "contract_type": "string",
        "publication_date": "string",
        "closing_date": "string",
        "status": "string",
        "recruiter_id": "integer"
      }
    },
    "message": "Job offer created successfully"
  }
  ```

#### Update Job Offer
- **URL**: `/api/v1/job-offers/{id}`
- **Method**: `PUT`
- **Authentication**: JWT (Recruiter role)
- **Request Body**:
  ```json
  {
    "title": "string",
    "description": "string",
    "location": "string",
    "salary": "float",
    "contract_type": "string",
    "requirements": "string",
    "benefits": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "job_offer": {
        "id": "integer",
        "title": "string",
        "description": "string",
        "location": "string",
        "salary": "float",
        "contract_type": "string",
        "publication_date": "string",
        "closing_date": "string",
        "status": "string",
        "recruiter_id": "integer"
      }
    },
    "message": "Job offer updated successfully"
  }
  ```

#### Deactivate Job Offer
- **URL**: `/api/v1/job-offers/{id}/deactivate`
- **Method**: `PATCH`
- **Authentication**: JWT (Recruiter role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": null,
    "message": "Job offer deactivated successfully"
  }
  ```

#### Permanently Delete Job Offer
- **URL**: `/api/v1/job-offers/{id}`
- **Method**: `DELETE`
- **Authentication**: JWT (Recruiter role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": null,
    "message": "Job offer permanently deleted"
  }
  ```

#### Get My Job Offers
- **URL**: `/api/v1/job-offers/my-offers`
- **Method**: `GET`
- **Authentication**: JWT (Recruiter role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "items": [
        {
          "id": "integer",
          "title": "string",
          "description": "string",
          "location": "string",
          "salary": "float",
          "contract_type": "string",
          "publication_date": "string",
          "closing_date": "string",
          "status": "string",
          "recruiter_id": "integer"
        }
      ],
      "total": "integer"
    },
    "message": null
  }
  ```

#### Get Applicants for Job Offer
- **URL**: `/api/v1/job-offers/{id}/applicants`
- **Method**: `GET`
- **Authentication**: JWT (Recruiter role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "applicants": [
        {
          "id": "integer",
          "candidate_id": "integer",
          "candidate": {
            "first_name": "string",
            "last_name": "string",
            "email": "string"
          },
          "job_offer_id": "integer",
          "application_status": "string",
          "comment": "string",
          "application_date": "string"
        }
      ]
    },
    "message": null
  }
  ```

## Applications

### Candidate Endpoints

#### Apply for Job
- **URL**: `/api/v1/applications`
- **Method**: `POST`
- **Authentication**: JWT (Candidate role)
- **Request Body**:
  ```json
  {
    "job_offer_id": "integer",
    "message": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "application": {
        "id": "integer",
        "candidate_id": "integer",
        "job_offer_id": "integer",
        "application_status": "string",
        "comment": "string",
        "application_date": "string"
      }
    },
    "message": "Application submitted successfully"
  }
  ```

#### Get My Applications
- **URL**: `/api/v1/my-applications`
- **Method**: `GET`
- **Authentication**: JWT (Candidate role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "applications": [
        {
          "id": "integer",
          "job_offer": {
            "id": "integer",
            "title": "string",
            "company": "string"
          },
          "application_status": "string",
          "application_date": "string"
        }
      ]
    },
    "message": null
  }
  ```

### Shared Endpoints

#### Get Application Details
- **URL**: `/api/v1/applications/{id}`
- **Method**: `GET`
- **Authentication**: JWT
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "application": {
        "id": "integer",
        "candidate_id": "integer",
        "job_offer_id": "integer",
        "job_offer": {
          "title": "string",
          "description": "string",
          "location": "string",
          "salary": "float"
        },
        "application_status": "string",
        "comment": "string",
        "application_date": "string"
      }
    },
    "message": null
  }
  ```

### Recruiter Endpoints

#### Update Application Status
- **URL**: `/api/v1/applications/{id}/status`
- **Method**: `PUT`
- **Authentication**: JWT (Recruiter role)
- **Request Body**:
  ```json
  {
    "status": "string",
    "comment": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "application": {
        "id": "integer",
        "application_status": "string",
        "comment": "string"
      }
    },
    "message": "Application status updated successfully"
  }
  ```

## Profile Management

### Candidate Endpoints

#### Get Work Experience
- **URL**: `/api/v1/profile/work-experience`
- **Method**: `GET`
- **Authentication**: JWT (Candidate role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "work_experience": [
        {
          "id": "integer",
          "company": "string",
          "position": "string",
          "duties": "string",
          "start_date": "string",
          "end_date": "string"
        }
      ]
    },
    "message": null
  }
  ```

#### Add Work Experience
- **URL**: `/api/v1/profile/work-experience`
- **Method**: `POST`
- **Authentication**: JWT (Candidate role)
- **Request Body**:
  ```json
  {
    "company": "string",
    "position": "string",
    "duties": "string",
    "start_date": "string",
    "end_date": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Work experience saved successfully",
      "work_experience": {
        "id": "integer",
        "company": "string",
        "position": "string",
        "duties": "string",
        "start_date": "string",
        "end_date": "string"
      }
    },
    "message": null
  }
  ```

#### Update Work Experience
- **URL**: `/api/v1/profile/work-experience/{id}`
- **Method**: `PUT`
- **Authentication**: JWT (Candidate role)
- **Request Body**:
  ```json
  {
    "company": "string",
    "position": "string",
    "duties": "string",
    "start_date": "string",
    "end_date": "string"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Work experience updated successfully",
      "work_experience": {
        "id": "integer",
        "company": "string",
        "position": "string",
        "duties": "string",
        "start_date": "string",
        "end_date": "string"
      }
    },
    "message": null
  }
  ```

#### Delete Work Experience
- **URL**: `/api/v1/profile/work-experience/{id}`
- **Method**: `DELETE`
- **Authentication**: JWT (Candidate role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Work experience deleted successfully"
    },
    "message": null
  }
  ```

#### Get Academic Background
- **URL**: `/api/v1/profile/academic-background`
- **Method**: `GET`
- **Authentication**: JWT (Candidate role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "academic_background": [
        {
          "id": "integer",
          "institution": "string",
          "degree": "string",
          "start_year": "integer",
          "end_year": "integer"
        }
      ]
    },
    "message": null
  }
  ```

#### Add Academic Background
- **URL**: `/api/v1/profile/academic-background`
- **Method**: `POST`
- **Authentication**: JWT (Candidate role)
- **Request Body**:
  ```json
  {
    "institution": "string",
    "degree": "string",
    "start_year": "integer",
    "end_year": "integer"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Academic background saved successfully",
      "academic_background": {
        "id": "integer",
        "institution": "string",
        "degree": "string",
        "start_year": "integer",
        "end_year": "integer"
      }
    },
    "message": null
  }
  ```

#### Update Academic Background
- **URL**: `/api/v1/profile/academic-background/{id}`
- **Method**: `PUT`
- **Authentication**: JWT (Candidate role)
- **Request Body**:
  ```json
  {
    "institution": "string",
    "degree": "string",
    "start_year": "integer",
    "end_year": "integer"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Academic background updated successfully",
      "academic_background": {
        "id": "integer",
        "institution": "string",
        "degree": "string",
        "start_year": "integer",
        "end_year": "integer"
      }
    },
    "message": null
  }
  ```

#### Delete Academic Background
- **URL**: `/api/v1/profile/academic-background/{id}`
- **Method**: `DELETE`
- **Authentication**: JWT (Candidate role)
- **Response**: 
  ```json
  {
    "success": true,
    "data": {
      "message": "Academic background deleted successfully"
    },
    "message": null
  }
  ```

## Error Handling

The API consistently returns error responses in the following format:

```json
{
  "success": false,
  "data": null,
  "message": "Error message description",
  "error": true
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Resource created
- `400`: Bad request (validation errors)
- `401`: Unauthorized (missing or invalid token)
- `403`: Forbidden (insufficient permissions)
- `404`: Resource not found
- `409`: Conflict (e.g., email already exists)
- `422`: Unprocessable entity (validation errors)
- `500`: Server error

## Implementation Patterns

### Repository Pattern
- **BaseModel**: Abstracts database operations
- **Specific Models**: Implement domain-specific data access

### Service Layer
- **Services**: Encapsulate business logic separate from controllers
- **Utils**: Provide reusable functionality (JWT handling, validation, etc.)

### Response Standardization
All responses follow a consistent structure:
```json
{
  "success": true|false,
  "data": object|null,
  "message": string|null,
  "error": boolean (present only for errors)
}
```

### Authentication & Authorization
- JWT-based authentication
- Role-based access control (Candidate/Recruiter)
- Token blacklisting for logout

## Development Guidelines

When extending the API, follow these guidelines to maintain SOLID principles:

1. **Create new controllers** for new resource types
2. **Extend BaseModel** for new data entities
3. **Use dependency injection** to provide services to controllers
4. **Validate input** before processing
5. **Standardize responses** using ResponseHandler
6. **Handle errors** consistently
7. **Document new endpoints** in this documentation 