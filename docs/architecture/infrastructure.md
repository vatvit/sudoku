# Infrastructure Architecture

## 📋 Purpose of This File

**For AI Assistants**: This file describes the infrastructure architecture including Docker containers, AWS ECS deployment, Terraform configurations, and infrastructure patterns. Use this when you need to understand deployment architecture, container orchestration, AWS services usage, or infrastructure-as-code patterns.

**For Developers**: This document explains the infrastructure setup and deployment architecture. Use it to understand how the application is containerized, deployed to AWS, and how to work with the infrastructure code.

**How to Use**:
- Reference this for deployment and infrastructure understanding
- Use it to understand container architecture and AWS services
- Follow the patterns for local development and staging deployment
- Use the Terraform examples to understand infrastructure configuration

---

# Infrastructure Architecture

## Overview

The infrastructure follows a modern containerized approach with Infrastructure as Code (IaC) using Terraform. The system supports both local development and AWS staging environments.

*For high-level system architecture, see [System Overview](overview.md)*

## Architecture Diagram

TODO: Add diagram

## Local Development Environment

### Docker Setup
```
infra/docker/
├── cache/              # Redis cache container
├── db/                 # MySQL database container
├── mercure/            # Mercure hub container
└── php/                # PHP application container
```

### Container Architecture

#### 1. PHP Application Container (`infra/docker/php/`)
```dockerfile
# Multi-stage build for optimization
FROM php:8.2-fpm AS base
# Install dependencies, extensions, and configure PHP

FROM base AS development
# Development-specific configurations
# XDebug setup for debugging

FROM base AS production
# Production optimizations
# Remove development tools
```

**Key Features:**
- **PHP 8.2+** with FPM
- **Nginx** web server
- **XDebug** for development debugging
- **Composer** for dependency management
- **Symfony Console** for CLI operations

#### 2. Database Container (`infra/docker/db/`)
```dockerfile
FROM mysql:8.1
# MySQL 8.1 with custom configuration
```

**Configuration:**
- **Database**: `sudoku`
- **User**: `sudoku_user`
- **Port**: `3306`
- **Initialization**: `init-db.sql`

#### 3. Cache Container (`infra/docker/cache/`)
```dockerfile
FROM redis:7-alpine
# Redis 7 with Alpine Linux
```

**Configuration:**
- **Port**: `6379`
- **Persistence**: RDB snapshots
- **Memory**: Configurable limits

#### 4. Mercure Container (`infra/docker/mercure/`)
```dockerfile
FROM dunglas/mercure
# Mercure hub with Caddy server
```

**Configuration:**
- **Transport**: Bolt database
- **JWT Authentication**: Publisher/Subscriber keys
- **CORS**: Development-friendly settings
- **Port**: `80`

### Local Infrastructure (Terraform)

**`infra/local/`** - Local development infrastructure
```hcl
# main.tf - Main Terraform configuration
# variables.tf - Variable definitions
# output.tf - Output values
# apply.sh - Automated apply script
```

**Key Resources:**
- **Docker Networks**: Inter-container communication
- **Volumes**: Persistent data storage
- **Environment Variables**: Configuration management

## AWS Staging Environment

### Infrastructure Components

The staging environment uses AWS managed services for scalability and reliability:

#### 1. Compute Layer
- **ECS Fargate**: Serverless container orchestration for PHP application and Mercure hub
- **Auto Scaling**: Automatic scaling based on CPU and memory usage
- **Task Definitions**: Container specifications with resource allocation

*Configuration: `infra/staging/ecs.tf`*

#### 2. Container Registry
- **ECR**: Private container registry for application images
- **Image Scanning**: Automated vulnerability scanning on push
- **Repositories**: Separate repositories for PHP app and Mercure hub

*Configuration: `infra/staging/ecr.tf`*

#### 3. Load Balancing
- **Application Load Balancer**: HTTP/HTTPS traffic distribution
- **Target Groups**: Health checks and traffic routing
- **SSL Termination**: HTTPS handling at the load balancer level

*Configuration: `infra/staging/elb.tf`*

#### 4. Database Layer
- **Aurora MySQL**: Managed MySQL-compatible database cluster
- **Multi-AZ**: High availability across availability zones
- **Automated Backups**: Point-in-time recovery with 7-day retention
- **Encryption**: Data encrypted at rest and in transit

*Configuration: `infra/staging/rds.tf`*

#### 5. Caching Layer
- **ElastiCache Redis**: Managed Redis cluster for session and cache data
- **T3 Instances**: Burstable performance instances
- **Security Groups**: Network-level access control
- **Subnet Groups**: VPC placement for network isolation

*Configuration: `infra/staging/elasticache.tf`*

#### 6. Networking
- **VPC**: Private network with public and private subnets
- **Security Groups**: Firewall rules for service-to-service communication
- **NAT Gateways**: Internet access for private resources
- **Multi-AZ**: Resources distributed across availability zones

*Configuration: `infra/staging/subnet.tf`, `infra/staging/security_groups.tf`*

### Deployment Process

#### 1. Build Process (`infra/staging/build.sh`)
```bash
#!/bin/bash
# Build and push Docker images to ECR

# Build PHP application
docker build -t sudoku_php ./src/backendApp
aws ecr get-login-password --region eu-central-1 | docker login --username AWS --password-stdin $ECR_REPO
docker tag sudoku_php:latest $ECR_REPO:latest
docker push $ECR_REPO:latest

# Build Mercure hub
docker build -t sudoku_mercure ./infra/docker/mercure
# Similar push process...
```

#### 2. Deployment Scripts (`infra/staging/deploy.sh`)
```bash
#!/bin/bash
# Deploy services to ECS

SERVICE_NAME=$1

# Update ECS service
aws ecs update-service \
  --cluster sudoku-cluster \
  --service $SERVICE_NAME \
  --force-new-deployment
```

#### 3. Infrastructure Management
```bash
# Initialize Terraform
terraform init

# Plan changes
terraform plan

# Apply infrastructure
terraform apply

# Destroy infrastructure
terraform destroy
```

## Configuration Management

### Environment Variables

**Local Development:**
```env
# Database
DATABASE_URL=mysql://sudoku_user:password@db:3306/sudoku

# Cache
REDIS_URL=redis://cache:6379

# Mercure
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLISHER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
MERCURE_SUBSCRIBER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
```

**AWS Staging:**
```env
# Database
DATABASE_URL=mysql://${RDS_USERNAME}:${RDS_PASSWORD}@${RDS_ENDPOINT}:3306/${RDS_DATABASE}

# Cache
REDIS_URL=redis://${ELASTICACHE_ENDPOINT}:6379

# Mercure
MERCURE_URL=https://${ALB_DNS_NAME}/.well-known/mercure
MERCURE_PUBLISHER_JWT_KEY=${MERCURE_JWT_KEY}
MERCURE_SUBSCRIBER_JWT_KEY=${MERCURE_JWT_KEY}
```

### Secrets Management

**AWS Secrets Manager:**
```hcl
resource "aws_secretsmanager_secret" "db_password" {
  name = "sudoku/db-password"
}

resource "aws_secretsmanager_secret_version" "db_password" {
  secret_id     = aws_secretsmanager_secret.db_password.id
  secret_string = random_password.db_password.result
}
```

## Monitoring and Logging

### CloudWatch Integration
- **Log Groups**: Centralized logging with 7-day retention
- **ECS Logs**: Container logs automatically sent to CloudWatch
- **Metrics**: CPU, memory, and custom application metrics

### Health Checks
- **Load Balancer**: HTTP health checks on `/status.php` endpoint
- **ECS Services**: Container health checks with automatic restart on failure
- **Monitoring**: Service availability and performance tracking
