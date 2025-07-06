# Backend Architecture

## 📋 Purpose of This File

**For AI Assistants**: This file describes the Symfony backend architecture, including CQRS patterns, domain-driven design, and internal implementation details. Use this when you need to understand backend code structure, command/query handlers, domain services, or internal backend patterns. This is NOT for API interface documentation.

**For Developers**: This document explains the backend implementation patterns and architecture decisions. Use it to understand how the backend is structured, how CQRS is implemented, and how to work with the domain layer, application layer, and infrastructure layer.

**How to Use**:
- Reference this for backend code organization and patterns
- Use it to understand CQRS implementation details
- Follow the layer descriptions for adding new features
- Use the code examples to understand implementation patterns

---

# Backend Architecture

## Overview

The backend is built with Symfony 6+ following Domain-Driven Design (DDD) principles and Command Query Responsibility Segregation (CQRS) pattern.

*For high-level system architecture, see [System Overview](overview.md)*

## Symfony Application Structure

The backend follows a clean architecture approach with clear separation of concerns:

### Application Layer (`src/Application/`)
- **CQRS**: Command and Query handlers for business operations
- **Services**: Application services orchestrating use cases
- **Validators**: Application-level validation logic

### Domain Layer (`src/Domain/`)
- **Services**: Core business logic and domain services
- **Value Objects**: Domain-specific value objects
- **DTOs**: Domain data transfer objects

### Infrastructure Layer (`src/Infrastructure/`)
- **Entities**: Doctrine ORM entities
- **Repositories**: Data access layer
- **Serializers**: Custom serialization logic
- **Validators**: Infrastructure-level validation

### Interface Layer (`src/Interface/`)
- **Controllers**: REST API endpoints and request handling

## CQRS Implementation

### Command Pattern
- **Purpose**: Write operations that modify system state
- **Location**: `src/Application/CQRS/Command/`
- **Pattern**: Commands with corresponding handlers using Symfony Messenger

### Query Pattern
- **Purpose**: Read operations that retrieve data
- **Location**: `src/Application/CQRS/Query/`
- **Pattern**: Queries with corresponding handlers for data retrieval

### Message Bus Integration
- **Implementation**: Symfony Messenger component
- **Utility**: `HandleMultiplyTrait` for message handling
- **Benefits**: Decoupled command/query processing

## Domain-Driven Design Layers

### 1. Domain Layer (`src/Domain/`)
- **Purpose**: Core business logic for Sudoku puzzle generation and game rules
- **Services**: Grid generation, cell hiding, grid shuffling, validation
- **Value Objects**: Cell coordinates and domain-specific values
- **DTOs**: Game actions, effects, cells, and cell groups

### 2. Application Layer (`src/Application/`)
- **Purpose**: Orchestrates domain logic and coordinates between layers
- **Services**: Game instance creation, retrieval, and real-time event publishing
- **CQRS**: Command and Query handlers for business operations
- **DTOs**: Data transfer objects with validation and mapping

*For detailed real-time communication implementation, see [Mercure Integration Documentation](../api/mercure-integration.md)*

### 3. Infrastructure Layer (`src/Infrastructure/`)
- **Purpose**: External concerns like database, caching, serialization
- **Entities**: Doctrine ORM entities for data persistence
- **Repositories**: Data access layer for entities
- **Components**: Custom serializers, validators, and lifecycle management

### 4. Interface Layer (`src/Interface/`)
- **Purpose**: REST API endpoints and request/response handling
- **Controllers**: Game instance management, action processing, configuration
- **Debug Endpoints**: Internal testing endpoints (not for production use)

## Caching Strategy

### Redis Integration
- **Game State Caching**: Active games stored in Redis
- **Session Data**: User sessions and temporary data
- **Performance Optimization**: Reduce database load 