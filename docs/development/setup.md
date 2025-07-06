# Development Setup Guide

## 📋 Purpose of This File

**For AI Assistants**: This file provides step-by-step instructions for setting up the development environment. Use this when you need to help developers get the project running locally, understand environment requirements, or troubleshoot setup issues. This includes both local development and staging environment setup.

**For Developers**: This document contains all the information needed to set up and run the project locally. Use it to get your development environment ready, understand the setup process, and troubleshoot common issues.

**How to Use**:
- Follow the prerequisites and installation steps
- Use the troubleshooting section for common issues
- Reference the environment variables and configuration sections
- Use the staging deployment section for production-like testing

---

# Development Setup Guide

## Prerequisites

Before setting up the project, ensure you have the following installed:

- **Docker**: Latest version with Docker Compose
- **Terraform**: Version 1.0+ for infrastructure management
- **AWS CLI**: For staging deployment (optional)
- **Node.js**: Version 18+ for frontend development
- **PHP**: Version 8.2+ for backend development (optional, Docker preferred)

*For infrastructure details, see [Infrastructure Architecture](../architecture/infrastructure.md)*

## Local Development Setup

### First Time Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd sudoku
```

2. **Initialize local infrastructure**
```bash
cd ./infra/local
terraform init
./apply.sh
./docker_exec_php.sh
```

3. **Install dependencies inside Docker container**
```bash
# Backend dependencies
cd /app/backendApp && composer install

# Run database migrations
./bin/console doctrine:migrations:migrate

# Frontend dependencies
cd /app/clientAppVue && npm install
cd /app/clientAppNext && npm install
```

4. **Start development servers**
```bash
# Start Vue.js development server
cd /app/clientAppVue && npm run dev

# Or start Next.js development server
cd /app/clientAppNext && npm run dev
```

5. **Access the application**
- **Frontend**: http://localhost
- **Backend API**: http://localhost/api
- **API Documentation**: http://localhost/api/openapi.json
- **Mercure Hub**: http://localhost/.well-known/mercure

### Daily Development Workflow

1. **Start the environment**
```bash
cd ./infra/local
./apply.sh
./docker_exec_php.sh
```

2. **Apply infrastructure changes**
```bash
terraform apply -auto-approve
```

3. **Stop the environment**
```bash
# Stop specific services
docker-compose stop

# Stop all services
docker-compose down
```

## Staging Environment Setup

### Prerequisites

1. **AWS Account**: Active AWS account with appropriate permissions
2. **AWS CLI Configuration**: Configured with access keys
3. **Terraform**: Version 1.0+ installed

### AWS Credentials Setup

Create file `/Users/vatvit/.aws/credentials` with:
```ini
[default]
aws_access_key_id=**************
aws_secret_access_key=************
```

Install AWS CLI: https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html

### Initial Deployment

1. **Initialize Terraform**
```bash
cd ./infra/staging
terraform init
```

2. **Deploy infrastructure**
```bash
terraform apply
```

3. **Build and deploy applications**
```bash
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

### Managing Staging Environment

#### Apply Infrastructure Changes
```bash
# Check for other deployments first
# Otherwise call ./stop.sh sudoku to stop the whole service

# Apply Terraform changes
terraform apply

# Apply Docker or codebase changes
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

#### Shutdown Staging Environment

**Temporary/Partial Shutdown:**
```bash
./stop.sh sudoku
./stop.sh sudoku_mercure
```

**Full Shutdown:**
```bash
terraform destroy
```

## Development Tools

### XDebug Configuration

1. **Enable Debug Helper**: Install [XDebug Helper](https://chromewebstore.google.com/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) Chrome extension

2. **Configure PHPStorm**:
   - Enable Debug Listening
   - Disable "Ignore external connections" in `Settings → PHP → Debug`

3. **Verify XDebug Setup**:
```bash
# Check if XDebug is installed
php -r "phpinfo();" | grep debug

# Verify configuration
php -r "var_dump(xdebug_info());"
```

### Database Management

**Access MySQL Database:**
```bash
# From host machine
docker exec -it sudoku-db-1 mysql -u sudoku_user -p sudoku

# From PHP container
mysql -h db -u sudoku_user -p sudoku
```

**Run Migrations:**
```bash
cd /app/backendApp
./bin/console doctrine:migrations:migrate
```

**Reset Database:**
```bash
cd /app/backendApp
./bin/console doctrine:database:drop --force
./bin/console doctrine:database:create
./bin/console doctrine:migrations:migrate
```

### Cache Management

**Access Redis Cache:**
```bash
# From host machine
docker exec -it sudoku-cache-1 redis-cli

# From PHP container
redis-cli -h cache
```

**Clear Application Cache:**
```bash
cd /app/backendApp
./bin/console cache:clear
```

## Troubleshooting

### XDebug Issues

**Common Problems:**
- Container MUST contain XDebug php extension
- XDebug `client_host` MUST be `host.docker.internal` for host communication
- XDebug `ide_key` should match PHPStorm configuration
- PHPStorm MUST NOT ignore external connections

**Verification Commands:**
```bash
# Check XDebug extension
php -r "phpinfo();" | grep debug

# Check XDebug configuration
php -r "var_dump(xdebug_info());"
```

### Docker Issues

**Clean up Docker resources:**
```bash
# Remove unused containers
docker container prune

# Remove unused images
docker image prune

# Remove unused volumes
docker volume prune

# Remove unused networks
docker network prune

# Clean everything
docker system prune -a
```

**Rebuild containers:**
```bash
cd ./infra/local
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database Issues

**Connection Problems:**
```bash
# Check database container status
docker ps | grep db

# Check database logs
docker logs sudoku-db-1

# Test database connection
docker exec -it sudoku-php-1 mysql -h db -u sudoku_user -p -e "SELECT 1;"
```

**Reset Database:**
```bash
cd /app/backendApp
./bin/console doctrine:database:drop --force
./bin/console doctrine:database:create
./bin/console doctrine:migrations:migrate
```

### Mercure Issues

**Check Mercure Hub:**
```bash
# Check Mercure container status
docker ps | grep mercure

# Check Mercure logs
docker logs sudoku-mercure-1

# Test Mercure endpoint
curl http://localhost/.well-known/mercure
```

**Mercure Configuration:**
- Verify JWT keys are set correctly
- Check CORS configuration for development
- Ensure transport URL is accessible

### Frontend Issues

**Vue.js Development:**
```bash
# Check Node.js version
node --version

# Clear npm cache
npm cache clean --force

# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

**Next.js Development:**
```bash
# Check Next.js version
npx next --version

# Clear Next.js cache
rm -rf .next

# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

## Environment Variables

### Local Development

Create `.env.local` files in respective directories:

**Backend (src/backendApp/.env.local):**
```env
DATABASE_URL=mysql://sudoku_user:password@db:3306/sudoku
REDIS_URL=redis://cache:6379
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLISHER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
MERCURE_SUBSCRIBER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
```

**Vue.js (src/clientAppVue/.env.local):**
```env
VITE_API_URL=http://localhost/api
VITE_MERCURE_URL=http://localhost/.well-known/mercure
```

**Next.js (src/clientAppNext/.env.local):**
```env
NEXT_PUBLIC_API_URL=http://localhost/api
NEXT_PUBLIC_MERCURE_URL=http://localhost/.well-known/mercure
```

### Staging Environment

Environment variables are managed through AWS ECS task definitions and Terraform.

## Performance Optimization

### Local Development

**Docker Resource Limits:**
- Increase memory allocation for containers
- Use volume mounts for better I/O performance
- Enable Docker BuildKit for faster builds

**Database Optimization:**
- Use connection pooling
- Optimize queries with proper indexing
- Use Redis for caching

**Frontend Optimization:**
- Enable hot module replacement
- Use source maps for debugging
- Optimize bundle size with code splitting

## Security Considerations

### Local Development

**Secrets Management:**
- Never commit sensitive data to version control
- Use environment variables for configuration
- Rotate development secrets regularly

**Network Security:**
- Use Docker networks for container isolation
- Restrict database access to application containers
- Use secure connections for external services

### Staging Environment

**AWS Security:**
- Use IAM roles with least privilege
- Enable VPC flow logs for network monitoring
- Use AWS Secrets Manager for sensitive data
- Enable CloudTrail for API logging 