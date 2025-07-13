# API Design Conventions

## REST Endpoint Patterns

### URL Structure
The API follows a hierarchical resource-based structure:

```
/api/
├── games/
│   └── sudoku/
│       └── instances/
│           ├── POST /                    # Create new game instance
│           ├── GET /{instanceId}         # Get game state
│           └── POST /{instanceId}/actions # Submit game action
└── config/                               # Application configuration
```

### HTTP Method Usage

**POST** - Create new resources or submit actions
- `POST /api/games/sudoku/instances` - Create new game
- `POST /api/games/sudoku/instances/{id}/actions` - Submit game action

**GET** - Retrieve resources
- `GET /api/games/sudoku/instances/{id}` - Get game state
- `GET /api/config` - Get application configuration

**PUT/PATCH** - Update resources (not currently used but reserved)
**DELETE** - Remove resources (not currently used but reserved)

### Resource Naming Conventions

- **Plural nouns** for collections: `/instances`, `/actions`
- **Hierarchical structure** for nested resources: `/games/sudoku/instances`
- **Kebab-case** for multi-word resources: `game-sudoku`
- **Consistent naming** across all endpoints

### Route Naming Convention

Symfony route names follow the pattern: `{action}-{resource-hierarchy}`

```php
#[Route(
    '/api/games/sudoku/instances',
    name: 'create-game-sudoku-instance',
    methods: ['POST']
)]

#[Route(
    '/api/games/sudoku/instances/{gameId}',
    name: 'get-game-sudoku-instance',
    methods: ['GET']
)]

#[Route(
    '/api/games/sudoku/instances/{gameId}/actions',
    name: 'create-game-sudoku-instance-action',
    methods: ['POST']
)]
```

## Response Format Standards

### Success Response Structure

**Standard JSON Response:**
```json
{
  "gameId": "550e8400-e29b-41d4-a716-446655440000",
  "grid": {
    "cells": [
      [{"value": 5, "protected": true}, {"value": 0, "protected": false}]
    ]
  },
  "cellGroups": [
    {"id": 1, "cells": ["1:1", "1:2"], "type": "ROW"}
  ],
  "status": "active"
}
```

**Response DTO Pattern:**
```php
class InstanceCreateResponseDto
{
    public function __construct(
        public readonly string $gameId,
        public readonly array $grid,
        public readonly array $cellGroups,
        public readonly string $status
    ) {}
}
```

### Error Response Structure

**Validation Error Response:**
```json
{
  "type": "https://symfony.com/errors/validation",
  "title": "Validation Failed",
  "status": 400,
  "detail": "size: Grid size must be 4, 9, or 16",
  "violations": [
    {
      "propertyPath": "size",
      "message": "Grid size must be 4, 9, or 16",
      "code": "INVALID_CHOICE_ERROR"
    }
  ]
}
```

**Business Logic Error Response:**
```json
{
  "type": "https://example.com/errors/game-not-found",
  "title": "Game Not Found",
  "status": 404,
  "detail": "Game instance with ID 'invalid-id' was not found",
  "code": "GAME_NOT_FOUND"
}
```

**System Error Response:**
```json
{
  "type": "https://example.com/errors/internal",
  "title": "Internal Server Error",
  "status": 500,
  "detail": "An unexpected error occurred",
  "code": "INTERNAL_SERVER_ERROR"
}
```

### Error Code Requirements

**All error responses MUST include an error code:**
- **Validation errors**: Use the `code` field within each violation object (e.g., `INVALID_CHOICE_ERROR`)
- **Business logic errors**: Include a `code` field at the root level using SCREAMING_SNAKE_CASE (e.g., `GAME_NOT_FOUND`, `INVALID_MOVE`, `GAME_ALREADY_COMPLETED`)
- **System errors**: Include a `code` field at the root level (e.g., `INTERNAL_SERVER_ERROR`, `SERVICE_UNAVAILABLE`, `DATABASE_CONNECTION_ERROR`)

**Error Code Naming Convention:**
- Use SCREAMING_SNAKE_CASE format
- Be descriptive and specific to the error condition
- Group related errors with common prefixes (e.g., `GAME_NOT_FOUND`, `GAME_ALREADY_COMPLETED`)
- Avoid generic codes like `ERROR` or `FAILURE`

**Common Error Codes:**
- `VALIDATION_FAILED` - General validation error
- `GAME_NOT_FOUND` - Game instance not found
- `INVALID_MOVE` - Invalid game move attempted
- `GAME_ALREADY_COMPLETED` - Action attempted on completed game
- `INTERNAL_SERVER_ERROR` - Unexpected system error
- `SERVICE_UNAVAILABLE` - Service temporarily unavailable

## OpenAPI Documentation Standards

### Required Annotations

**Controller Method Documentation:**
```php
#[OA\Response(
    response: 200,
    description: 'Game instance created successfully',
    content: new OA\JsonContent(ref: new Model(type: InstanceCreateResponseDto::class))
)]
#[OA\Response(
    response: 400,
    description: 'Validation error',
    content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
)]
#[OA\RequestBody(
    description: 'Game instance creation parameters',
    required: true,
    content: new OA\JsonContent(ref: new Model(type: InstanceCreateRequestDto::class))
)]
#[OA\Tag(name: 'game-sudoku')]
#[OA\Tag(name: 'post-data')]
public function create(
    #[MapRequestPayload] InstanceCreateRequestDto $requestDto
): JsonResponse
```

### Parameter Documentation

**Path Parameters:**
```php
#[OA\Parameter(
    name: 'gameId',
    description: 'Unique game instance identifier',
    in: 'path',
    required: true,
    schema: new OA\Schema(type: 'string', format: 'uuid')
)]
```

**Request Body Documentation:**
```php
#[OA\RequestBody(
    description: 'Game action to be processed',
    required: true,
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'type', type: 'string', enum: ['SET_VALUE', 'SET_NOTE']),
            new OA\Property(property: 'coords', type: 'string', example: '1:1'),
            new OA\Property(property: 'value', type: 'integer', minimum: 1, maximum: 9)
        ]
    )
)]
```

### Response Model Definitions

**DTO with OpenAPI Annotations:**
```php
class InstanceGetResponseDto
{
    public function __construct(
        #[OA\Property(description: 'Unique game identifier', example: '550e8400-e29b-41d4-a716-446655440000')]
        public readonly string $gameId,

        #[OA\Property(description: 'Current game grid state')]
        public readonly array $grid,

        #[OA\Property(description: 'Cell group definitions for validation')]
        public readonly array $cellGroups,

        #[OA\Property(description: 'Current game status', enum: ['active', 'completed', 'paused'])]
        public readonly string $status
    ) {}
}
```

### Tag Organization

**Consistent Tag Usage:**
- `game-sudoku` - All Sudoku game related endpoints
- `game-sudoku-instances` - Game instance management
- `game-sudoku-instance-actions` - Game action processing
- `post-data` - Endpoints that accept POST data
- `get-data` - Endpoints that return data

## Request/Response Validation

### Input Validation Patterns

**DTO Validation:**
```php
class ActionDto
{
    public function __construct(
        #[Assert\Choice(choices: ['SET_VALUE', 'SET_NOTE', 'CLEAR_CELL'], message: 'Invalid action type')]
        public readonly string $type,

        #[Assert\Regex(pattern: '/^\d+:\d+$/', message: 'Coordinates must be in format "row:col"')]
        public readonly string $coords,

        #[Assert\Range(min: 1, max: 16, message: 'Value must be between 1 and 16')]
        #[Assert\Type(type: 'integer')]
        public readonly ?int $value = null
    ) {}
}
```

**Custom Validation Constraints:**
```php
#[Assert\Callback]
public function validateGameAction(ExecutionContextInterface $context): void
{
    if ($this->type === 'SET_VALUE' && $this->value === null) {
        $context->buildViolation('Value is required for SET_VALUE action')
            ->atPath('value')
            ->addViolation();
    }
}
```

### Response Validation

**Consistent Data Types:**
- `string` for IDs, coordinates, status values
- `integer` for numeric values, counts
- `boolean` for flags and states
- `array` for collections and complex objects
- `null` for optional values

**Date/Time Format:**
```json
{
  "createdAt": "2024-01-15T10:30:00Z",
  "updatedAt": "2024-01-15T10:35:00Z"
}
```

## Authentication Requirements

### Current State (Development)
- **Public API**: No authentication required for basic operations
- **Real-time Communication**: JWT-based authentication for Mercure publishers/subscribers

### Future Authentication Plans
- **Player Authentication**: JWT-based user authentication
- **Game Access Control**: Players can only access their own games
- **Rate Limiting**: API rate limiting per user/IP
- **CORS Configuration**: Proper cross-origin request handling

### JWT Token Structure (Mercure)
```json
{
  "mercure": {
    "publish": ["game/{gameId}"],
    "subscribe": ["game/{gameId}"]
  },
  "exp": 1640995200
}
```

## Frontend API Integration

### Generated Client Usage

**API Client Configuration:**
```typescript
import { Api } from '@/generated/Api'

const api = new Api({
  baseURL: process.env.VUE_APP_API_URL || 'http://localhost/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})
```

**Error Handling Strategy:**
```typescript
class ApiService {
  async createGameInstance(request: InstanceCreateRequestDto): Promise<InstanceCreateResponseDto> {
    try {
      const response = await this.api.games.createGameSudokuInstance(request)
      return response.data
    } catch (error) {
      if (error.response?.status === 400) {
        throw new ValidationError(error.response.data.violations)
      } else if (error.response?.status === 404) {
        throw new NotFoundError(error.response.data.detail)
      } else {
        throw new ApiError('An unexpected error occurred')
      }
    }
  }
}
```

### Type Safety Integration

**Generated Type Usage:**
```typescript
import type { 
  InstanceCreateRequestDto, 
  InstanceCreateResponseDto,
  ActionDto 
} from '@/generated/data-contracts'

// Type-safe API calls
const createRequest: InstanceCreateRequestDto = {
  size: 9,
  difficulty: 'medium'
}

const response: InstanceCreateResponseDto = await apiService.createGameInstance(createRequest)
```

## Caching Considerations

### HTTP Caching Headers

**Cache Control for Static Data:**
```php
#[Route('/api/config', methods: ['GET'])]
public function config(): JsonResponse
{
    $response = $this->json($configData);
    $response->setMaxAge(3600); // Cache for 1 hour
    return $response;
}
```

**No-Cache for Dynamic Data:**
```php
#[Route('/api/games/sudoku/instances/{gameId}', methods: ['GET'])]
public function getInstance(string $gameId): JsonResponse
{
    $response = $this->json($gameData);
    $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
    return $response;
}
```

### API Versioning Strategy

**URL Versioning (Future):**
```
/api/v1/games/sudoku/instances
/api/v2/games/sudoku/instances
```

**Header Versioning (Alternative):**
```
Accept: application/json; version=1
Accept: application/json; version=2
```

## Performance Optimization

### Response Optimization
- **Minimal Data**: Only return necessary fields
- **Pagination**: For large collections (future implementation)
- **Compression**: Enable gzip compression
- **CDN**: Use CDN for static assets

### Request Optimization
- **Batch Operations**: Combine multiple actions when possible
- **Conditional Requests**: Use ETags for conditional updates
- **Request Validation**: Validate early to avoid processing

These conventions ensure consistent, maintainable, and well-documented API design across the entire application.
