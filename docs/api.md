# Cliente Feliz API - Documentation

Esta documentación describe los endpoints disponibles en la API RESTful de Cliente Feliz, una plataforma de gestión de selección de personal.

## Base URL

```
http://localhost:8080/api/v1
```

## API Versions

La API utiliza versionamiento en la URL. La versión actual es `v1`.

```
http://localhost:8080/api/v1
```

## General Routes

### Get API Information

```
GET /api
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "api": "Cliente Feliz API",
    "version": "v1",
    "description": "API RESTful para el sistema de selección de personal Cliente Feliz",
    "status": "active",
    "documentation": "/docs/api.md",
    "banner": " _____ _ _            _          _____     _ _     \n/  __ \\ (_)          | |        |  ___|   | (_)    \n...",
    "endpoints": {
      "auth": "/api/v1/auth/...",
      "job_offers": "/api/v1/job-offers/...",
      "applications": "/api/v1/applications/..."
    }
  }
}
```

## Authentication

La API utiliza autenticación JWT (JSON Web Token). Los tokens se obtienen a través del endpoint de login y deben incluirse en el encabezado `Authorization` para las peticiones a endpoints protegidos.

### Authentication Endpoints

#### Login

```
POST /api/v1/auth/login
```

**Parámetros de solicitud:**

```json
{
  "email": "usuario@ejemplo.com",
  "password": "contraseña"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1...",
    "user": {
      "id": 1,
      "first_name": "Juan",
      "last_name": "Perez",
      "email": "juan.perez@clientefeliz.com",
      "role": "Reclutador"
    }
  }
}
```

#### Register

```
POST /api/v1/auth/register
```

**Parámetros de solicitud:**

```json
{
  "first_name": "Nombre",
  "last_name": "Apellido",
  "email": "usuario@ejemplo.com",
  "password": "contraseña",
  "role": "Candidato",
  "birth_date": "1990-01-01",
  "phone": "56912345678",
  "address": "Dirección"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1...",
    "user": {
      "id": 6,
      "first_name": "Nombre",
      "last_name": "Apellido",
      "email": "usuario@ejemplo.com",
      "role": "Candidato"
    }
  }
}
```

#### Get Current User

```
GET /api/v1/auth/user
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "Juan",
    "last_name": "Perez",
    "email": "juan.perez@clientefeliz.com",
    "role": "Reclutador"
  }
}
```

#### Logout

```
POST /api/v1/auth/logout
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "success": true,
    "message": "Logged out successfully"
  }
}
```

## Job Offers

### Public Endpoints

#### Get Active Job Offers

```
GET /api/v1/job-offers
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Desarrollador Full Stack",
      "description": "Buscamos desarrollador con experiencia en PHP, JavaScript y React",
      "location": "Santiago",
      "salary": "1500000.00",
      "contract_type": "Indefinido",
      "publication_date": "2023-05-10",
      "closing_date": "2023-06-10",
      "status": "Vigente",
      "recruiter_id": 1,
      "recruiter_name": "Juan",
      "recruiter_lastname": "Perez"
    },
    ...
  ]
}
```

#### Get Job Offer Details

```
GET /api/v1/job-offers/{id}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Desarrollador Full Stack",
    "description": "Buscamos desarrollador con experiencia en PHP, JavaScript y React",
    "location": "Santiago",
    "salary": "1500000.00",
    "contract_type": "Indefinido",
    "publication_date": "2023-05-10",
    "closing_date": "2023-06-10",
    "status": "Vigente",
    "recruiter_id": 1,
    "recruiter_name": "Juan",
    "recruiter_lastname": "Perez"
  }
}
```

### Recruiter Endpoints

#### Create Job Offer

```
POST /api/v1/job-offers
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "title": "Desarrollador PHP",
  "description": "Buscamos desarrollador con experiencia en PHP y MySQL",
  "location": "Santiago",
  "salary": 1200000,
  "contract_type": "Indefinido",
  "closing_date": "2023-06-30"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 5,
    "title": "Desarrollador PHP",
    "description": "Buscamos desarrollador con experiencia en PHP y MySQL",
    "location": "Santiago",
    "salary": "1200000.00",
    "contract_type": "Indefinido",
    "publication_date": "2023-05-10",
    "closing_date": "2023-06-30",
    "status": "Vigente",
    "recruiter_id": 1
  }
}
```

#### Update Job Offer

```
PUT /api/v1/job-offers/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "title": "Desarrollador PHP Senior",
  "salary": 1500000,
  "status": "Cerrada"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 5,
    "title": "Desarrollador PHP Senior",
    "description": "Buscamos desarrollador con experiencia en PHP y MySQL",
    "location": "Santiago",
    "salary": "1500000.00",
    "contract_type": "Indefinido",
    "publication_date": "2023-05-10",
    "closing_date": "2023-06-30",
    "status": "Cerrada",
    "recruiter_id": 1
  }
}
```

#### Deactivate Job Offer

```
PATCH /api/v1/job-offers/{id}/deactivate
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": null,
  "message": "Oferta laboral desactivada exitosamente"
}
```

#### Permanently Delete Job Offer

```
DELETE /api/v1/job-offers/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": null,
  "message": "Oferta laboral eliminada permanentemente"
}
```

#### Get My Job Offers

```
GET /api/v1/job-offers/my-offers
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "title": "Desarrollador Full Stack",
        "description": "...",
        "location": "Santiago",
        "salary": "1500000.00",
        "contract_type": "Indefinido",
        "publication_date": "2023-05-10",
        "closing_date": "2023-06-10",
        "status": "Vigente",
        "recruiter_id": 1
      },
      ...
    ],
    "total": 3
  }
}
```

#### Get Applicants for Job Offer

```
GET /api/v1/job-offers/{id}/applicants
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "candidate_id": 3,
        "job_offer_id": 1,
        "application_status": "Revisando",
        "comment": "Candidato con buen nivel técnico",
        "application_date": "2023-05-10 10:30:00",
        "update_date": "2023-05-11 14:20:00",
        "first_name": "Pedro",
        "last_name": "Sanchez",
        "email": "pedro.sanchez@gmail.com"
      },
      ...
    ],
    "total": 5
  }
}
```

## Applications

### Candidate Endpoints

#### Apply for Job Offer

```
POST /api/v1/applications
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "job_offer_id": 1
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 5,
    "candidate_id": 3,
    "job_offer_id": 1,
    "application_status": "Postulando",
    "comment": null,
    "application_date": "2023-05-10 15:45:00",
    "update_date": "2023-05-10 15:45:00"
  }
}
```

#### Get My Applications

```
GET /api/v1/applications/my-applications
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "candidate_id": 3,
        "job_offer_id": 1,
        "application_status": "Revisando",
        "comment": "Candidato con buen nivel técnico",
        "application_date": "2023-05-10 10:30:00",
        "update_date": "2023-05-11 14:20:00",
        "title": "Desarrollador Full Stack",
        "description": "...",
        "location": "Santiago",
        "salary": "1500000.00"
      },
      ...
    ],
    "total": 3
  }
}
```

#### Get Application Details

```
GET /api/v1/applications/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "candidate_id": 3,
    "job_offer_id": 1,
    "application_status": "Revisando",
    "comment": "Candidato con buen nivel técnico",
    "application_date": "2023-05-10 10:30:00",
    "update_date": "2023-05-11 14:20:00"
  }
}
```

### Recruiter Endpoints

#### Update Application Status

```
PUT /api/v1/applications/{id}/status
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "status": "Entrevista Psicologica",
  "comment": "Candidato pasó etapa de revisión, programar entrevista"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "candidate_id": 3,
    "job_offer_id": 1,
    "application_status": "Entrevista Psicologica",
    "comment": "Candidato pasó etapa de revisión, programar entrevista",
    "application_date": "2023-05-10 10:30:00",
    "update_date": "2023-05-12 09:15:00"
  }
}
```

## Profile

### Work Experience Endpoints

#### Get Work Experience

```
GET /api/v1/profile/work-experience
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 3,
      "company": "Empresa ABC",
      "position": "Desarrollador Web",
      "duties": "Desarrollo de aplicaciones web con PHP y MySQL",
      "start_date": "2020-01-15",
      "end_date": "2022-03-30"
    },
    ...
  ]
}
```

#### Add Work Experience

```
POST /api/v1/profile/work-experience
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "company": "Empresa XYZ",
  "position": "Desarrollador Frontend",
  "duties": "Desarrollo de interfaces con React",
  "start_date": "2022-04-01",
  "end_date": "2023-05-01"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "user_id": 3,
    "company": "Empresa XYZ",
    "position": "Desarrollador Frontend",
    "duties": "Desarrollo de interfaces con React",
    "start_date": "2022-04-01",
    "end_date": "2023-05-01"
  }
}
```

#### Update Work Experience

```
PUT /api/v1/profile/work-experience/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "company": "Empresa XYZ",
  "position": "Desarrollador Frontend Senior",
  "duties": "Desarrollo de interfaces con React y liderazgo de equipo",
  "start_date": "2022-04-01",
  "end_date": "2023-05-01"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "user_id": 3,
    "company": "Empresa XYZ",
    "position": "Desarrollador Frontend Senior",
    "duties": "Desarrollo de interfaces con React y liderazgo de equipo",
    "start_date": "2022-04-01",
    "end_date": "2023-05-01"
  }
}
```

#### Delete Work Experience

```
DELETE /api/v1/profile/work-experience/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": null,
  "message": "Experiencia laboral eliminada exitosamente"
}
```

### Academic Background Endpoints

#### Get Academic Background

```
GET /api/v1/profile/academic-background
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 3,
      "institution": "Universidad de Chile",
      "degree": "Ingeniería en Informática",
      "field_of_study": "Informática",
      "start_year": "2015",
      "end_year": "2020"
    },
    ...
  ]
}
```

#### Add Academic Background

```
POST /api/v1/profile/academic-background
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "institution": "Universidad Católica",
  "degree": "Magister",
  "field_of_study": "Ciencias de la Computación",
  "start_year": "2020",
  "end_year": "2022"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "user_id": 3,
    "institution": "Universidad Católica",
    "degree": "Magister",
    "field_of_study": "Ciencias de la Computación",
    "start_year": "2020",
    "end_year": "2022"
  }
}
```

#### Update Academic Background

```
PUT /api/v1/profile/academic-background/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Parámetros de solicitud:**

```json
{
  "institution": "Universidad Católica",
  "degree": "Magister en Ciencias",
  "field_of_study": "Ciencias de la Computación",
  "start_year": "2020",
  "end_year": "2022"
}
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "user_id": 3,
    "institution": "Universidad Católica",
    "degree": "Magister en Ciencias",
    "field_of_study": "Ciencias de la Computación",
    "start_year": "2020",
    "end_year": "2022"
  }
}
```

#### Delete Academic Background

```
DELETE /api/v1/profile/academic-background/{id}
```

**Encabezado requerido:**

```
Authorization: eyJhbGciOiJIUzI1...
```

**Respuesta exitosa:**

```json
{
  "success": true,
  "data": null,
  "message": "Formación académica eliminada exitosamente"
}
```

## HTTP Response Codes

- **200 OK**: Solicitud exitosa
- **201 Created**: Recurso creado exitosamente
- **400 Bad Request**: Solicitud inválida
- **401 Unauthorized**: Autenticación fallida o token inválido
- **403 Forbidden**: No tiene permisos para acceder al recurso
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Internal Server Error**: Error del servidor

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