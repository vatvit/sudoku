# System Architecture Overview

## 📋 Purpose of This File

**For AI Assistants**: This file provides the high-level system architecture and technology stack. Use this when you need to understand the overall system design, component relationships, and technology choices. This is the starting point for understanding how the entire system works together.

**For Developers**: This document gives you the big picture of the system architecture. Use it to understand component interactions, technology decisions, and system-wide patterns before diving into specific implementation details.

**How to Use**:
- Start here when learning about the system architecture
- Reference this for technology stack decisions
- Use the diagrams and flow descriptions to understand system behavior
- Refer to specific architecture documents for implementation details

---

# System Architecture Overview

## High-Level Architecture

The Sudoku application follows a modern microservices-inspired architecture with clear separation of concerns:

**System Components:**
- **Frontend Applications**: Vue.js, Next.js, and future mobile apps
- **Backend API**: Symfony-based REST API handling game logic and data persistence
- **Real-time Communication**: Mercure hub for live game synchronization
- **Data Storage**: MySQL for persistent game state, Redis for session data and caching, file storage for assets
- **Communication Flow**: Frontend apps → Backend API → Data storage, with real-time updates flowing Backend → Mercure → Frontend

## Core Components

### 1. Frontend Applications
- **Vue.js App** (`src/clientAppVue/`): Primary frontend with real-time game interface
- **Next.js App** (`src/clientAppNext/`): Alternative frontend implementation
- Both communicate with the backend via REST API and Mercure for real-time updates

### 2. Backend Application (`src/backendApp/`)
- **Symfony 6+** with PHP 8+
- **CQRS Pattern**: Separate command and query handlers
- **Domain-Driven Design**: Clear layer separation
- **REST API**: JSON-based communication
- **Mercure Integration**: Real-time event publishing

### 3. Real-time Communication
- **Mercure Hub**: Server-sent events for live game updates
- **JWT Authentication**: Secure publisher/subscriber authentication
- **Event-driven Architecture**: Actions trigger real-time updates

*For detailed Mercure integration, see [Mercure Integration](../api/mercure-integration.md)*

### 4. Data Layer
- **MySQL Database**: Persistent game state and user data
- **Redis Cache**: Session data and temporary game state
- **Doctrine ORM**: Database abstraction and entity management

## Key Design Principles

### 1. Event-Driven Architecture
- **Pattern**: Internal backend pattern using Symfony Messenger for decoupling modules and services
- **Implementation**: Commands and queries are dispatched through MessageBus with handlers processing them asynchronously
- **Location**: `src/backendApp/src/Application/CQRS/` - Command/Query handlers with `#[AsMessageHandler]` attributes
- **Benefits**: Loose coupling between components, easier testing, and modular architecture
- **Reference**: [Symfony Messenger Documentation](https://symfony.com/doc/current/messenger.html)

### 2. CQRS Pattern
- **Pattern**: Command Query Responsibility Segregation - separates read and write operations
- **Implementation**: Commands for state changes, Queries for data retrieval, both using Symfony Messenger
- **Location**: `src/backendApp/src/Application/CQRS/Command/` and `src/backendApp/src/Application/CQRS/Query/`
- **Reference**: [CQRS Pattern Overview](https://martinfowler.com/bliki/CQRS.html)

### 3. Domain-Driven Design
- **Pattern**: Domain-Driven Design with clear layer separation and bounded contexts
- **Implementation**: Domain layer contains core business logic, Application layer orchestrates use cases, Infrastructure handles external concerns
- **Location**: `src/backendApp/src/Domain/` - Core business logic, `src/backendApp/src/Application/` - Use cases, `src/backendApp/src/Infrastructure/` - External concerns
- **Reference**: [Domain-Driven Design Fundamentals](https://martinfowler.com/bliki/DomainDrivenDesign.html)

### 4. Stateless Design
- Backend services are stateless
- Session data stored in Redis
- Horizontal scaling capability

## Technology Stack Details

### Backend Stack
- **Framework**: Symfony 6+ with PHP 8+
- **ORM**: Doctrine with MySQL
- **Caching**: Redis with Symfony Cache component
- **Real-time**: Mercure hub with JWT authentication
- **Validation**: Symfony Validator with custom constraints
- **API Documentation**: Nelmio API Doc Bundle

*For detailed backend architecture, see [Backend Architecture](backend-architecture.md)*

### Frontend Stack
- **Vue.js 3**: Composition API, TypeScript
- **Next.js**: React-based alternative
- **State Management**: Pinia (Vue) / React Context
- **Real-time**: EventSource API for Mercure subscription
- **Styling**: Tailwind CSS

*For detailed frontend architecture, see [Frontend Architecture](frontend-architecture.md)*

### Infrastructure Stack
- **Containerization**: Docker with multi-stage builds
- **Orchestration**: AWS ECS with Fargate
- **Database**: AWS RDS (MySQL)
- **Caching**: AWS ElastiCache (Redis)
- **Load Balancing**: AWS Application Load Balancer
- **Infrastructure as Code**: Terraform

*For detailed infrastructure setup, see [Infrastructure Architecture](infrastructure.md)*
