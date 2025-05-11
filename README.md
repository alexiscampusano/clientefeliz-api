# Personnel Selection Management API - Cliente Feliz

RESTful API for the personnel selection system of "Cliente Feliz" company.

## Documentation

- [API Documentation](docs/api.md) - Complete guide of endpoints, parameters and usage examples

## Technologies Used

- PHP 8.1
- MySQL 8.0
- Apache
- Docker and Docker Compose

## Docker Setup

### Prerequisites

- Docker
- Docker Compose

### Installation Instructions

1. Clone this repository:

```bash
git clone https://github.com/alexiscampusano/clientefeliz-api.git
cd clientefeliz-api
```

2. Build and start the containers:

```bash
docker-compose up -d
```

3. The application will be available at:

- API: http://localhost:8080
- phpMyAdmin: http://localhost:8081
  - Username: cliente_feliz_user
  - Password: cliente_feliz_password

### API Endpoints

#### Authentication

- Register: `POST /api/v1/auth/register`
- Login: `POST /api/v1/auth/login`
- Get current user: `GET /api/v1/auth/user`
- Logout: `POST /api/v1/auth/logout`

#### Job Offers (public)

- View active offers: `GET /api/v1/job-offers`
- View offer details: `GET /api/v1/job-offers/{id}`

#### Job Offers (recruiters only)

- Create offer: `POST /api/v1/job-offers`
- Update offer: `PUT /api/v1/job-offers/{id}`
- Deactivate offer: `PATCH /api/v1/job-offers/{id}/deactivate`
- Permanently delete offer: `DELETE /api/v1/job-offers/{id}`
- My offers: `GET /api/v1/job-offers/my-offers`
- View applicants: `GET /api/v1/job-offers/{id}/applicants`

#### Applications

- Apply to offer: `POST /api/v1/applications`
- Update status: `PUT /api/v1/applications/{id}/status`
- My applications: `GET /api/v1/applications/my-applications`
- View application details: `GET /api/v1/applications/{id}`

#### Profile Management (candidates only)

- Get work experience: `GET /api/v1/profile/work-experience`
- Add work experience: `POST /api/v1/profile/work-experience`
- Update work experience: `PUT /api/v1/profile/work-experience/{id}`
- Delete work experience: `DELETE /api/v1/profile/work-experience/{id}`
- Get academic background: `GET /api/v1/profile/academic-background`
- Add academic background: `POST /api/v1/profile/academic-background`
- Update academic background: `PUT /api/v1/profile/academic-background/{id}`
- Delete academic background: `DELETE /api/v1/profile/academic-background/{id}`

### Test Data

The database is automatically initialized with the data defined in `database/database.sql`.

## Local Development without Docker

If you prefer to run the application without Docker:

1. Import the database from `database/database.sql` to your local MySQL server.
2. Configure the database credentials in `api/config/Database.php`.
3. Make sure Apache or any other web server is configured to serve the `public` folder.

## Project Structure

- `api/`: Contains the API code
  - `config/`: Configuration (database)
  - `controllers/`: Common controllers (non-versioned)
  - `models/`: Models
  - `utils/`: Utilities (JWT, validation, etc.)
  - `v1/`: API version 1
    - `controllers/`: Version 1 specific controllers
    - `routes/`: Version 1 specific routes
    - `services/`: Version 1 specific services
  - `bootstrap.php`: API initialization
  - `routes.php`: Common routes definition and version loading
- `database/`: SQL for database initialization
- `docker/`: Docker configuration files
- `public/`: Application entry point
  - `assets/`: Static resources like ASCII banner

## API Versioning

The API is organized in independent versions:

- Current version routes (v1): `/api/v1/...`

This structure allows adding new versions (v2, v3, etc.) without affecting existing versions. To create a new version, simply:

1. Create new folders `api/v2/controllers`, `api/v2/routes`, etc.
2. Define version-specific routes in `api/v2/routes/routes.php`
3. Modify `bootstrap.php` to load the new version files

## License

This project is licensed under the MIT License. 