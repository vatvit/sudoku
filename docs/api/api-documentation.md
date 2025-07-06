# API Documentation

## 📋 Purpose of This File

**For AI Assistants**: This file describes the REST API architecture, endpoints, data models, and interface contracts. Use this when you need to understand API design patterns, endpoint structure, request/response formats, authentication, error handling, or API-specific architectural decisions. This is about the API interface, NOT internal backend implementation.

**For Developers**: This document explains the API design and structure. Use it to understand how to interact with the API, what endpoints are available, how data is formatted, and how to handle errors and authentication.

**How to Use**:
- Reference this for API endpoint documentation and usage
- Use it to understand API design patterns and data models
- Follow the integration guidelines for best practices
- Use the OpenAPI specification for detailed schemas and testing

---

# API Documentation

## Overview

The Sudoku API provides a RESTful interface for real-time Sudoku game management with live synchronization capabilities.

## API Architecture

### Design Principles

1. **RESTful Design**: Standard HTTP methods and status codes
2. **Real-time Updates**: Server-sent events for live synchronization
3. **Stateless**: No server-side session management
4. **JSON-First**: All data exchange in JSON format

### API Structure

```
/api/
├── games/
│   └── sudoku/
│       └── instances/
│           ├── POST /                    # Create game instance
│           ├── GET /{instanceId}         # Get game state
│           └── POST /{instanceId}/actions # Submit game action
└── config/                               # Application configuration
```

> **Note**: Internal debug endpoints (e.g., `/api/mercure/publish`) are not documented here as they are for development/testing purposes only and should not be used in production integrations.

## API Groups

### 1. Game Instance Management
Core game lifecycle management including creation and state retrieval.

### 2. Game Actions
Handle player interactions and game state modifications.

### 3. Configuration
Provide application configuration and metadata.

> **📋 Complete API Reference**: For detailed endpoint documentation, request/response schemas, and interactive testing, see the [OpenAPI Specification](#openapi-specification).

## Data Models

The API uses JSON data models for all requests and responses. Complete schema definitions are available in the [OpenAPI Specification](#openapi-specification).

Key data models include:
- **Game Instance**: Game metadata and state
- **Game Action**: Player interactions and moves
- **Game State**: Current puzzle state and validation

## Authentication & Security

### Current State (Temporary)
- **Public API**: No authentication required for basic operations
- **Real-time Communication**: JWT-based authentication for publishers/subscribers

> **⚠️ Future Authentication Plans**: 
> - Player authentication will be implemented to restrict game access
> - Players will only be able to modify their own games
> - Some endpoints may become unavailable or limited for unauthenticated users
> - This is important for future API development and integration planning

### Security Features
- Input validation and sanitization
- CORS configuration for cross-origin requests
- JWT token management for real-time communication

## Error Handling

The API uses standard HTTP status codes and returns structured error responses. Complete error schemas and status codes are documented in the [OpenAPI Specification](#openapi-specification).

Error categories include validation errors, business logic violations, and system errors.

## OpenAPI Specification

The complete API reference with detailed schemas, examples, and interactive testing is available in the OpenAPI specification:

### Source File
- **OpenAPI YAML**: [src/backendApp/resources/openapi.yaml](../src/backendApp/resources/openapi.yaml)

### Access Points
- **Local Development**: [http://localhost/api/openapi.json](http://localhost/api/openapi.json)
- **Staging Environment**: [https://api.example.com/api/openapi.json](https://api.example.com/api/openapi.json)

### What's Included
- Complete endpoint documentation with parameters
- Request/response schemas and examples
- Interactive testing interface
- Error response definitions
- Authentication requirements
- Data model specifications 