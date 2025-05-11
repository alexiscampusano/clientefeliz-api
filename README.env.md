# Run this command to set a secure JWT key
echo JWT_SECRET=$(openssl rand -base64 32) >> .env

# Environment variables used in the project
- DB_HOST: Database host
- DB_NAME: Database name
- DB_USER: Database user
- DB_PASSWORD: Database password
- APP_ENV: Application environment (development, production)
- APP_DEBUG: Enable debugging (true, false)
- APP_URL: Base URL of the application
- JWT_SECRET: Secret key for signing JWT tokens
- JWT_EXPIRATION: JWT token expiration time in seconds
