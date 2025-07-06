# Sudoku Project

A modern Sudoku application built with Symfony backend, Vue.js/Next.js frontends, and real-time communication via Mercure.

## Quick Start

For detailed setup instructions, see the [Development Setup Guide](docs/development/setup.md).

### Local Development
```bash
cd ./infra/local
terraform init
./apply.sh
./docker_exec_php.sh
```

### Staging Deployment
```bash
cd ./infra/staging
terraform init
terraform apply
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

## Documentation

📚 **Complete documentation is available in the [docs/](docs/) directory:**

- **[System Overview](docs/architecture/overview.md)** - High-level architecture and technology stack
- **[Backend Architecture](docs/architecture/backend-architecture.md)** - Symfony backend patterns and CQRS implementation
- **[Frontend Architecture](docs/architecture/frontend-architecture.md)** - Vue.js and Next.js frontend patterns
- **[Infrastructure](docs/architecture/infrastructure.md)** - Docker, AWS ECS, and deployment architecture
- **[Development Setup](docs/development/setup.md)** - Local and staging environment setup
- **[API Documentation](docs/api/api-documentation.md)** - REST API endpoints and data structures

## Project Structure

```
sudoku/
├── src/
│   ├── backendApp/          # Symfony 6+ backend application
│   ├── clientAppVue/        # Vue.js 3 frontend application
│   └── clientAppNext/       # Next.js frontend application
├── infra/
│   ├── local/              # Local development infrastructure (Terraform)
│   ├── staging/            # AWS staging infrastructure (Terraform)
│   └── docker/             # Docker configurations
└── docs/                   # Project documentation
```

## Key Features

- **Real-time Gameplay**: Live updates via Mercure hub
- **Multi-Frontend Support**: Vue.js and Next.js implementations
- **Modern Architecture**: CQRS, Domain-Driven Design, Event-driven
- **Containerized Deployment**: Docker containers on AWS ECS
- **Infrastructure as Code**: Terraform for environment management

## Technology Stack

- **Backend**: Symfony 6+, PHP 8+, Doctrine ORM, Mercure
- **Frontend**: Vue.js 3, Next.js, TypeScript, Tailwind CSS
- **Infrastructure**: Docker, AWS ECS, RDS, ElastiCache, Terraform
- **Real-time**: Mercure hub for server-sent events

## Quick Links

- **Local Development**: http://localhost
- **API Documentation**: http://localhost/api/openapi.json
- **Mercure Hub**: http://localhost/.well-known/mercure


