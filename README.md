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

# How to run Local:

## First time:

```shell
cd ./infra/local
terraform init
./apply.sh
./docker_exec_php.sh
```

Run inside the Docker container

```shell
cd /app/backendApp && composer install
./bin/console doctrine:migrations:migrate
cd /app/clientAppVue && npm install
cd /app/clientAppNext && npm install
```

Depends on which a Front framework is needed:
```shell
cd /app/clientAppVue && npm install
npm run dev
```

open http://localhost

# How to run Staging

## From the Scratch

Create file `/Users/vatvit/.aws/credentials` with

```
[default]
aws_access_key_id=**************
aws_secret_access_key=************
```

Install AWS CLI https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html

```shell
cd ./infra/staging
terraform init
terraform apply
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

# How to apply changes to Local

```shell
terraform apply -auto-approve
```

# How to apply changes to Staging

**Check no other Deployments in progress**
Otherwise call `./stop.sh sudoku` to stop the whole service and then deploy it again. 

## Terraform changes
```shell
terraform apply
```
## Docker or Codebase changes
```shell
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

# How to Shutdown Staging

## Temporary / Partially

```shell
./stop.sh sudoku
./shop.sh sudoku_mercure
```

## Fully

```shell
terraform destroy
```

## XDebug on Local
Enable [Debug Helper](https://chromewebstore.google.com/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) Chrome extension
Enable Debug Listening in PHPStorm

# Troubleshooting

## XDebug

* Container MUST contain XDebug php extension. Use `php -r "phpinfo();" | grep debug` to verify.
* XDebug configuration key in container `client_host` MUST be `host.docker.internal` to communicate with Host machine. Use `XDEBUG_CLIENT_HOST` env var in the Container.
* (additionally can be configured, but should work without it) XDebug configuration key in container `ide_key` MUST be the same as `PHPSTORM`. Use `XDEBUG_IDE_KEY` env var in the Container.
* PHPStorm MUST NOT ignore external connections. Disable checkbox in `Settings -> PHP -> Debug`.

## Docker
* Try to clean-up images
