# Project Context for AI Assistants

## Business Domain

**Sudoku Puzzle Game Application**
- Real-time Sudoku puzzle game (single-player, with potential future multiplayer support)
- Multi-frontend architecture for platform simulation and framework comparison
- Live game synchronization with server-sent events
- Game state management with Redis caching for active games and SQL for backup/save functionality
- Focus on clean architecture and modern development practices
- Fast-paced gameplay with typical sessions lasting 5-20 minutes

## Key Business Rules

### Sudoku Game Logic
- Standard 9x9 Sudoku grid with 3x3 sub-blocks
- Support for multiple grid sizes (4x4, 9x9, 16x16)
- Cell validation: each row, column, and block must contain unique numbers
- Protected cells (pre-filled) vs user-editable cells
- Notes/pencil marks functionality for solving assistance
- Real-time validation and mistake detection

### Game State Management
- Active game instances stored in Redis cache for fast access during gameplay
- SQL database serves as backup storage and for save game functionality
- Session-based game access (future: user-based authentication)
- Game actions tracked for undo/redo functionality
- Real-time state updates optimized for short, fast-paced game sessions (5-20 minutes)

### User Interaction Patterns
- Cell selection and value input
- Notes mode for pencil marks
- Hover effects for related cells (same row/column/block)
- Value highlighting across the grid
- Mistake indication with visual feedback
- Real-time updates for responsive gameplay experience

## Architecture Decisions

### Why Clean Architecture Was Chosen
- **Separation of Concerns**: Clear boundaries between domain, application, and infrastructure layers
- **Testability**: Domain logic isolated from external dependencies
- **Maintainability**: Changes in one layer don't affect others
- **Scalability**: Easy to add new features without breaking existing code

### CQRS Implementation Rationale
- **Read/Write Separation**: Optimized queries vs commands
- **Event-Driven Architecture**: Decoupled components using Symfony Messenger
- **Scalability**: Independent scaling of read and write operations
- **Audit Trail**: Command history for game actions and undo/redo

### Multi-Frontend Approach Reasoning
- **Platform Simulation**: Different frontends simulate various platform experiences
- **Framework Comparison**: Direct comparison between Vue.js and Next.js implementations
- **Technology Flexibility**: Vue.js as primary, Next.js as alternative
- **Team Skills**: Support different frontend expertise
- **Future Expansion**: Easy to add mobile apps or other clients
- **API-First Design**: Backend agnostic to frontend technology

### Caching Strategy Rationale
- **Performance**: Redis for active game state reduces database load
- **Real-time Requirements**: Fast access to game state for live updates
- **Session Management**: Temporary game data in cache
- **Scalability**: Horizontal scaling with shared cache layer

## Technology Stack Context

### Backend (Symfony 7 + PHP 8.2+)
- **Framework Choice**: Symfony for enterprise-grade features and ecosystem
- **PHP Version**: 8.2+ for modern language features and performance
- **Database**: MySQL with Doctrine ORM for relational data
- **Cache**: Redis for session data and active game state
- **Real-time**: Mercure hub for server-sent events
- **API Documentation**: Nelmio API Doc Bundle with OpenAPI

### Frontend Technologies
- **Vue.js 3**: Primary frontend with Composition API and TypeScript
- **Next.js 15**: Alternative React-based implementation
- **State Management**: Pinia (Vue) for reactive state management
- **Real-time**: EventSource API for Mercure subscription
- **Styling**: Tailwind CSS for utility-first styling
- **Testing**: Vitest (Vue) and Playwright for E2E

### Infrastructure
- **Containerization**: Docker with multi-stage builds
- **Local Development**: Docker files with Terraform
- **Staging/Production**: AWS ECS with Fargate
- **Database**: AWS RDS (MySQL)
- **Cache**: AWS ElastiCache (Redis)
- **Load Balancing**: AWS Application Load Balancer

## Development Philosophy

### Code Quality Standards
- **Type Safety**: Full TypeScript on frontend, strict PHP typing on backend
- **Testing**: Unit, integration, and E2E tests with high coverage
- **Code Style**: PSR-12 (PHP), ESLint + Prettier (TypeScript)
- **Static Analysis**: PHPStan for PHP, TypeScript compiler for frontend
- **Documentation**: Comprehensive inline documentation and architectural docs

### Performance Considerations
- **Caching Strategy**: Multi-layer caching (Redis, HTTP, browser)
- **Real-time Optimization**: Efficient Mercure topic subscription
- **Bundle Optimization**: Vite for fast frontend builds
- **Database Optimization**: Doctrine query optimization and indexing

### Security Approach
- **Input Validation**: Symfony Validator with custom constraints
- **JWT Authentication**: For Mercure publisher/subscriber access
- **CORS Configuration**: Proper cross-origin request handling
- **Future Authentication**: Player-based game access control planned

## Key Patterns and Conventions

### Backend Patterns
- **Domain Services**: Core business logic in domain layer
- **Application Services**: Use case orchestration
- **DTOs**: Data transfer between layers with validation
- **Mappers**: Entity-to-DTO conversion with clear separation
- **Event Handlers**: Asynchronous processing with Symfony Messenger

### Frontend Patterns
- **Composition API**: Modern Vue.js with `<script setup>`
- **Reactive State**: Centralized game state management
- **Component Communication**: Props down, events up
- **API Integration**: Generated TypeScript client from OpenAPI
- **Error Handling**: Centralized error management with user feedback

### Testing Patterns
- **Arrange-Act-Assert**: Consistent test structure
- **Mock Dependencies**: Isolated unit testing
- **Data Providers**: Parameterized tests for multiple scenarios
- **Integration Tests**: API endpoint testing with real dependencies
- **E2E Tests**: Critical user flow validation

This context provides the foundation for understanding the project's business domain, technical decisions, and development approach. Use this information to provide relevant, context-aware assistance for development tasks.
