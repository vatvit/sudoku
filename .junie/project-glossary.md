# Project Glossary

## Domain Terms (Sudoku Game)

### Core Game Concepts

**Sudoku Grid**
- The main game board consisting of cells arranged in a square matrix
- Standard size: 9x9 (81 cells total)
- Alternative sizes: 4x4 (16 cells), 16x16 (256 cells)
- Divided into sub-blocks (boxes) for validation

**Cell**
- Individual unit within the Sudoku grid
- Contains either a value (1-9 for standard Sudoku) or is empty
- Identified by coordinates in "row:column" format (e.g., "1:1", "9:9")
- Can be protected (pre-filled) or editable by the user

**Cell Coordinates**
- String format: "row:col" (e.g., "1:1" for top-left, "9:9" for bottom-right)
- Row and column numbers start from 1, not 0
- Used throughout the system for cell identification and validation

**Protected Cell**
- Pre-filled cell that cannot be modified by the user
- Part of the initial puzzle setup
- Provides clues for solving the puzzle
- Visually distinguished from user-editable cells

**Cell Groups**
- Collections of cells that must contain unique values
- Three types: Rows, Columns, and Blocks (sub-grids)
- Each group must contain all numbers from 1 to grid size exactly once

**Block (Sub-grid)**
- Square sub-section of the main grid
- Size: √(grid_size) × √(grid_size)
- Standard 9x9 grid has 9 blocks of 3x3 each
- Also called "box" in some Sudoku terminology

**Notes (Pencil Marks)**
- Small numbers written in cells as solving aids
- Represent possible values for that cell
- Can be toggled on/off by the user
- Displayed in a 3x3 mini-grid within each cell

### Game States and Actions

**Game Instance**
- A single Sudoku game session
- Has unique identifier (UUID)
- Contains grid state, difficulty level, and metadata
- Stored in cache during active play

**Game Status**
- Current state of a game instance
- Values: `active`, `completed`, `paused`
- Determines available actions and UI behavior

**Game Action**
- User interaction that modifies game state
- Types: `SET_VALUE`, `SET_NOTE`, `CLEAR_CELL`
- Contains coordinates, value, and timestamp
- Used for undo/redo functionality

**Action History**
- Chronological record of all game actions
- Enables undo/redo functionality
- Stored temporarily during game session
- Persisted for completed games

**Validation**
- Process of checking if a cell value violates Sudoku rules
- Checks row, column, and block constraints
- Real-time validation provides immediate feedback

**Mistake**
- Invalid cell value that violates Sudoku rules
- Visually highlighted to the user
- Can be corrected by changing or clearing the cell value

### Difficulty and Generation

**Difficulty Level**
- Determines puzzle complexity
- Currently not implemented in the codebase
- Would affect number of pre-filled cells and solving techniques required when implemented

**Grid Generation**
- Process of creating a valid, complete Sudoku solution
- Uses mathematical algorithms to ensure unique solution
- Involves shuffling and constraint satisfaction

**Grid Shuffling**
- Randomization process applied to base grid patterns
- Maintains mathematical validity while creating variety
- Applied multiple times to increase randomness

**Cell Hiding**
- Process of removing cells from complete grid to create puzzle
- Number of hidden cells determines difficulty
- Must ensure puzzle has unique solution

## Technical Terms (Architecture)

### Backend Architecture

**Clean Architecture**
- Architectural pattern separating concerns into layers
- Layers: Domain, Application, Infrastructure, Interface
- Promotes testability and maintainability

**Domain Layer**
- Core business logic and rules
- Contains entities, value objects, and domain services
- Independent of external frameworks and technologies

**Application Layer**
- Orchestrates use cases and business workflows
- Contains application services and CQRS handlers
- Coordinates between domain and infrastructure layers

**Infrastructure Layer**
- External concerns like database, cache, and file system
- Contains repositories, entities, and external service adapters
- Implements interfaces defined in other layers

**Interface Layer**
- Entry points to the application (controllers, CLI commands)
- Handles HTTP requests and responses
- Contains DTOs and request/response mapping

**CQRS (Command Query Responsibility Segregation)**
- Pattern separating read and write operations
- Commands modify state, Queries retrieve data
- Implemented using Symfony Messenger component

**Command**
- Object representing an intent to change system state
- Immutable data structure with validation
- Processed by corresponding CommandHandler

**Query**
- Object representing a request for data
- Read-only operation that doesn't modify state
- Processed by corresponding QueryHandler

**Message Bus**
- Infrastructure for dispatching commands and queries
- Decouples message creation from processing
- Enables middleware like validation and logging

**DTO (Data Transfer Object)**
- Simple object for transferring data between layers
- Contains validation rules and type information
- Used for API requests, responses, and internal communication

**Mapper**
- Component responsible for converting between object types
- Transforms entities to DTOs and vice versa
- Maintains separation between layers

### Frontend Architecture

**Composition API**
- Modern Vue.js approach for component logic
- Uses functions instead of options object
- Provides better TypeScript support and reusability

**Reactive State**
- Vue.js system for automatic UI updates
- Uses `ref()` and `reactive()` for state management
- Automatically tracks dependencies and triggers re-renders

**Pinia Store**
- State management library for Vue.js applications
- Replaces Vuex with simpler, more intuitive API
- Provides TypeScript support and devtools integration

**Composable**
- Reusable function that encapsulates reactive state and logic
- Vue.js pattern for sharing functionality between components
- Promotes code reuse and separation of concerns

**Component Props**
- Data passed from parent to child components
- Immutable within child component
- Typed using TypeScript interfaces

**Component Events**
- Mechanism for child-to-parent communication
- Emitted by child components, handled by parents
- Maintains unidirectional data flow

### Real-time Communication

**Mercure**
- Protocol for real-time web updates
- Uses Server-Sent Events (SSE) for browser communication
- Enables live game synchronization between players

**Server-Sent Events (SSE)**
- Web standard for server-to-client real-time communication
- One-way communication from server to browser
- Automatically handles reconnection and error recovery

**Topic-based Messaging**
- Mercure pattern for organizing real-time updates
- Clients subscribe to specific topics (e.g., "game/123")
- Server publishes updates to relevant topics

**Event Source**
- Browser API for consuming Server-Sent Events
- Handles connection management and message parsing
- Provides event listeners for different message types

**Publisher/Subscriber Pattern**
- Messaging pattern where publishers send messages to topics
- Subscribers receive messages from topics they're interested in
- Decouples message producers from consumers

### Data Storage and Caching

**Redis Cache**
- In-memory data store used for caching
- Stores active game states for fast access
- Provides session storage and temporary data

**Cache Key Strategy**
- Naming convention for cache entries
- Format: `game|instance|sudoku|{gameId}`
- Enables efficient cache invalidation and lookup

**Cache TTL (Time To Live)**
- Expiration time for cached data
- Prevents stale data and manages memory usage
- Configured per cache entry type

**Doctrine ORM**
- Object-Relational Mapping library for PHP
- Maps database tables to PHP objects
- Provides query builder and migration tools

**Entity**
- PHP class representing database table
- Contains properties, relationships, and metadata
- Managed by Doctrine ORM

**Repository**
- Data access layer for entities
- Contains custom query methods
- Abstracts database operations from business logic

### API and Integration

**REST API**
- Architectural style for web services
- Uses HTTP methods (GET, POST, PUT, DELETE)
- Stateless communication with JSON payloads

**OpenAPI Specification**
- Standard for describing REST APIs
- Generates documentation and client code
- Enables API testing and validation

**API Client Generation**
- Automatic creation of TypeScript client from OpenAPI spec
- Provides type-safe API calls in frontend
- Keeps frontend and backend in sync

**Request/Response DTOs**
- Data structures for API communication
- Include validation rules and documentation
- Separate from internal domain objects

**HTTP Status Codes**
- Standard codes indicating request results
- 200 (OK), 201 (Created), 400 (Bad Request), 404 (Not Found), 500 (Server Error)
- Used consistently across all API endpoints

### Testing and Quality

**Unit Testing**
- Testing individual components in isolation
- Uses mocks and stubs for dependencies
- Fast execution and focused scope

**Integration Testing**
- Testing component interactions
- Uses real dependencies where appropriate
- Validates system behavior end-to-end

**End-to-End (E2E) Testing**
- Testing complete user workflows
- Uses browser automation tools
- Validates user experience and functionality

**Test Fixtures**
- Predefined data for testing
- Provides consistent test environment
- Includes mock objects and sample data

**Mock Objects**
- Fake implementations of dependencies
- Used in unit tests for isolation
- Configured to return specific responses

**Data Providers**
- PHPUnit feature for parameterized tests
- Allows testing multiple scenarios with same test logic
- Improves test coverage and maintainability

### Development and Deployment

**Docker Container**
- Lightweight, portable application package
- Includes application code and dependencies
- Ensures consistent environment across systems

**Docker Compose**
- Tool for defining multi-container applications
- Orchestrates database, cache, and application containers
- Simplifies local development setup

**Terraform**
- Infrastructure as Code tool
- Defines cloud resources in configuration files
- Manages infrastructure lifecycle and changes

**Blue-Green Deployment**
- Deployment strategy using two identical environments
- Reduces downtime and enables quick rollbacks
- Switches traffic between environments

**Health Checks**
- Automated tests for system availability
- Monitors application and infrastructure status
- Triggers alerts and automated responses

This glossary provides a comprehensive reference for understanding the terminology used throughout the Sudoku project, covering both domain-specific concepts and technical implementation details.