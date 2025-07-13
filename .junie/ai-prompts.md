# Effective AI Prompts for This Project

## Code Generation Prompts

### Backend Development

**Generate Symfony Controller:**
```
Generate a Symfony controller following the project's CQRS pattern for managing Sudoku game statistics. The controller should:
- Follow the existing naming convention (e.g., StatisticsController)
- Use OpenAPI annotations for documentation
- Include proper route definitions with tags
- Use MapRequestPayload for request DTOs
- Dispatch commands/queries through MessageBus
- Handle both success and error responses
- Include endpoints for: getting user statistics, updating game completion stats
```

**Generate Domain Service:**
```
Create a domain service for Sudoku puzzle difficulty calculation following the project's clean architecture. The service should:
- Be placed in App\Domain\Sudoku\Service namespace
- Calculate difficulty based on number of pre-filled cells and solving techniques required
- Use dependency injection for any external dependencies
- Include proper type declarations and PHPDoc
- Follow the existing domain service patterns
- Include validation for input parameters
```

**Generate CQRS Command and Handler:**
```
Generate a CQRS command and handler for completing a Sudoku game following the project patterns:
- Command: CompleteGameCommand with gameId, completionTime, and moveCount
- Handler: CompleteGameCommandHandler with proper validation
- Use Symfony Messenger attributes
- Include proper error handling and logging
- Update game status and persist completion data
- Publish Mercure event for real-time updates
```

### Frontend Development

**Generate Vue.js Component:**
```
Create a Vue.js component for displaying Sudoku game statistics using the project's patterns:
- Use Composition API with <script setup lang="ts">
- Include proper TypeScript interfaces for props
- Use Pinia store for state management
- Follow the existing CSS scoping patterns
- Include proper error handling and loading states
- Use the generated API client for data fetching
- Include accessibility attributes
```

**Generate Pinia Store:**
```
Create a Pinia store for managing Sudoku game statistics following the project patterns:
- Use the composition API style with defineStore
- Include reactive state for statistics data
- Implement computed properties for derived data
- Add actions for fetching and updating statistics
- Include proper error handling and loading states
- Use the generated API client
- Follow TypeScript best practices
```

**Generate API Service Integration:**
```
Create a service class for integrating with the Sudoku statistics API:
- Use the generated API client from OpenAPI
- Include proper error handling and retry logic
- Implement caching for frequently accessed data
- Add TypeScript interfaces for all data structures
- Follow the existing API service patterns
- Include proper logging and debugging support
```

## Code Review Prompts

### Architecture Review

**Review Clean Architecture Compliance:**
```
Review this code against the project's clean architecture principles:
- Check layer separation (Domain, Application, Infrastructure, Interface)
- Verify dependency direction (outer layers depend on inner layers)
- Ensure domain logic is not leaking into other layers
- Check for proper use of dependency injection
- Validate that interfaces are defined in the correct layers
- Look for violations of the dependency inversion principle

[Paste code here]
```

**Review CQRS Implementation:**
```
Review this CQRS implementation for compliance with project patterns:
- Check command/query separation
- Verify proper use of Symfony Messenger
- Ensure handlers are properly registered with attributes
- Check for side effects in query handlers
- Validate command immutability
- Review error handling and validation patterns

[Paste code here]
```

### API Review

**Review OpenAPI Documentation:**
```
Review this API endpoint for OpenAPI documentation completeness:
- Check for proper response documentation with status codes
- Verify request body documentation with examples
- Ensure parameter documentation is complete
- Check for proper error response documentation
- Validate tag usage and organization
- Review security requirements if applicable

[Paste code here]
```

**Review API Design Consistency:**
```
Review this API endpoint against the project's REST conventions:
- Check URL structure and resource naming
- Verify HTTP method usage
- Review request/response DTO design
- Check error handling and status codes
- Validate input validation patterns
- Ensure consistency with existing endpoints

[Paste code here]
```

### Frontend Review

**Review Vue.js Component:**
```
Review this Vue.js component against the project's best practices:
- Check Composition API usage and patterns
- Verify TypeScript type safety
- Review state management with Pinia
- Check component communication patterns (props/events)
- Validate CSS scoping and styling approach
- Review accessibility and user experience
- Check error handling and edge cases

[Paste code here]
```

**Review State Management:**
```
Review this Pinia store implementation:
- Check store structure and organization
- Verify reactive state management
- Review computed properties and getters
- Check action implementation and side effects
- Validate error handling and loading states
- Review TypeScript usage and type safety

[Paste code here]
```

## Debugging and Analysis Prompts

### Backend Debugging

**Analyze Performance Issue:**
```
Analyze this performance issue in the Symfony application:
- The API endpoint /api/games/sudoku/instances/{id} is responding slowly
- Symfony profiler shows high database query time
- Current response time is 2-3 seconds, should be under 500ms
- Here's the relevant code and profiler data:

[Paste code and profiler information]

Please identify potential bottlenecks and suggest optimizations following the project's patterns.
```

**Debug Cache Issues:**
```
Help debug this Redis cache issue in the Sudoku application:
- Game state is not being cached properly
- Cache hits are low despite frequent game state requests
- Here's the cache service implementation:

[Paste cache service code]

Please identify issues and suggest fixes following the project's caching patterns.
```

### Frontend Debugging

**Debug Vue.js Reactivity:**
```
Help debug this Vue.js reactivity issue:
- Game state updates are not reflecting in the UI
- Pinia store is being updated but components don't re-render
- Here's the relevant component and store code:

[Paste component and store code]

Please identify reactivity issues and suggest fixes following Vue.js best practices.
```

**Debug API Integration:**
```
Debug this API integration issue in the Vue.js frontend:
- API calls are failing intermittently
- Error handling is not working as expected
- Here's the API service and error handling code:

[Paste API service code]

Please identify issues and suggest improvements following the project's patterns.
```

## Testing Prompts

### Backend Testing

**Generate PHPUnit Tests:**
```
Generate comprehensive PHPUnit tests for this Sudoku domain service:
- Include unit tests with proper mocking
- Add data providers for different scenarios
- Test both success and failure cases
- Follow the project's testing patterns (Arrange-Act-Assert)
- Include edge cases and boundary conditions

[Paste service code]
```

**Generate Integration Tests:**
```
Create integration tests for this Symfony API endpoint:
- Test successful requests with valid data
- Test validation errors with invalid data
- Test authentication and authorization if applicable
- Include database interactions and state verification
- Follow the project's acceptance testing patterns

[Paste controller code]
```

### Frontend Testing

**Generate Vitest Component Tests:**
```
Generate comprehensive Vitest tests for this Vue.js component:
- Test component rendering with different props
- Test user interactions and event handling
- Test integration with Pinia store
- Include snapshot testing where appropriate
- Test error states and edge cases

[Paste component code]
```

**Generate E2E Tests:**
```
Create Playwright E2E tests for this Sudoku game feature:
- Test complete user workflow from game creation to completion
- Include positive and negative test scenarios
- Test real-time updates and multiplayer features
- Verify accessibility and responsive design
- Follow the project's E2E testing patterns

Feature description: [Describe the feature to test]
```

## Refactoring Prompts

### Code Improvement

**Refactor for Better Performance:**
```
Refactor this code for better performance while maintaining the project's architecture:
- Identify performance bottlenecks
- Suggest caching strategies
- Optimize database queries
- Improve algorithm efficiency
- Maintain clean architecture principles

[Paste code to refactor]
```

**Refactor for Better Testability:**
```
Refactor this code to improve testability:
- Reduce coupling between components
- Extract dependencies for easier mocking
- Improve separation of concerns
- Make code more modular
- Follow SOLID principles

[Paste code to refactor]
```

### Architecture Improvements

**Suggest Architecture Improvements:**
```
Review this module and suggest architecture improvements:
- Identify violations of clean architecture
- Suggest better layer separation
- Recommend design pattern applications
- Improve dependency management
- Enhance maintainability and extensibility

[Paste module code]
```

## Feature Development Prompts

### New Feature Planning

**Plan New Feature Implementation:**
```
Help plan the implementation of a new Sudoku feature: [Feature description]

Consider the following aspects:
- Domain modeling and business rules
- API design and endpoints needed
- Database schema changes
- Frontend components and user experience
- Real-time updates via Mercure
- Testing strategy
- Performance implications

Provide a step-by-step implementation plan following the project's architecture.
```

**Design API for New Feature:**
```
Design a REST API for this new Sudoku feature: [Feature description]

Follow the project's API conventions:
- Resource-based URL structure
- Proper HTTP methods and status codes
- Request/response DTO design
- OpenAPI documentation
- Error handling patterns
- Validation rules
- Integration with existing endpoints
```

## Documentation Prompts

**Generate API Documentation:**
```
Generate comprehensive API documentation for this endpoint:
- Include detailed description and use cases
- Provide request/response examples
- Document all parameters and their constraints
- Include error scenarios and responses
- Add integration examples for frontend

[Paste endpoint code]
```

**Create Technical Documentation:**
```
Create technical documentation for this feature:
- Explain the business logic and rules
- Document the technical implementation
- Include architecture diagrams if needed
- Provide usage examples
- Add troubleshooting guide

[Describe feature or paste code]
```

## Best Practices for Using These Prompts

1. **Provide Context**: Always include relevant code, error messages, or project context
2. **Be Specific**: Clearly state what you want to achieve and any constraints
3. **Reference Patterns**: Mention existing project patterns to follow
4. **Include Examples**: Provide examples of similar implementations in the project
5. **Specify Requirements**: List any specific requirements or constraints
6. **Ask for Explanations**: Request explanations of suggested solutions
7. **Iterate**: Use follow-up prompts to refine and improve suggestions

These prompts are designed to work with the specific architecture, patterns, and conventions used in this Sudoku project, ensuring consistent and high-quality AI assistance.