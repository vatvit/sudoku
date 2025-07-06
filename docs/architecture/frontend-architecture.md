# Frontend Architecture

## 📋 Purpose of This File

**For AI Assistants**: This file describes the frontend architecture including Vue.js and Next.js applications, component patterns, state management, and frontend-specific implementation details. Use this when you need to understand frontend code structure, component organization, state management patterns, or frontend-specific architectural decisions.

**For Developers**: This document explains the frontend implementation patterns and architecture decisions. Use it to understand how the Vue.js and Next.js applications are structured, how components are organized, and how to work with the frontend codebase.

**How to Use**:
- Reference this for frontend code organization and patterns
- Use it to understand component architecture and state management
- Follow the patterns described for adding new frontend features
- Use the code examples to understand implementation approaches

---

# Frontend Architecture

## Overview

The project includes two frontend implementations:
- **Vue.js 3 Application** (`src/clientAppVue/`): Primary frontend with real-time game interface
- **Next.js Application** (`src/clientAppNext/`): Alternative React-based implementation

Both frontends communicate with the Symfony backend via REST API and Mercure for real-time updates.

*For high-level system architecture, see [System Overview](overview.md)*

## Vue.js Application Architecture

### Project Structure
```
src/clientAppVue/
├── src/
│   ├── components/           # Vue components
│   │   ├── Sudoku/          # Game-specific components
│   │   └── ...              # Other components
│   ├── modules/             # Business logic modules
│   ├── router/              # Vue Router configuration
│   ├── App.vue              # Root component
│   └── main.ts              # Application entry point
├── e2e/                     # End-to-end tests
└── public/                  # Static assets
```

### Component Architecture

#### 1. Game Components
- **Puzzle.vue**: Main game component handling game state and logic
- **Cell.vue**: Individual cell component for user interactions
- **CellGroup.ts**: Cell grouping logic for rows, columns, and blocks

#### 2. State Management
- **Store Pattern**: Modular state management using TypeScript classes
- **Reactive State**: Vue 3 Composition API for reactive data
- **Event Handling**: Centralized event management for game interactions

#### 3. Real-time Communication
- **Mercure Integration**: Server-sent events for live game updates
- **EventSource API**: Browser-native API for subscribing to real-time events
- **Topic-based Subscription**: Frontend subscribes to game-specific topics for updates
- **Automatic Reconnection**: Built-in reconnection handling for network issues

*For detailed Mercure integration, see [Mercure Integration Documentation](../api/mercure-integration.md)*



## Next.js Application Architecture

### Project Structure
```
src/clientAppNext/
├── src/
│   └── app/                  # App Router (Next.js 13+)
├── public/                   # Static assets
└── package.json              # Dependencies
```

### Key Features
- **App Router**: Next.js 13+ app directory structure
- **TypeScript**: Full TypeScript support
- **Tailwind CSS**: Utility-first styling
- **Server Components**: React Server Components support

## Common Patterns

### 1. API Communication
- **HTTP Client**: Centralized API client with error handling
- **Runtime Configuration**: API endpoints configured via backend `/api/config` endpoint
- **Type Safety**: TypeScript interfaces for API responses

### 2. Real-time Communication
- **Mercure Integration**: Server-sent events for live game updates
- **EventSource API**: Browser-native API for subscribing to real-time events
- **Topic-based Subscription**: Frontend subscribes to game-specific topics for updates

*For detailed Mercure integration, see [Mercure Integration Documentation](../api/mercure-integration.md)*

### 3. State Management
- **Reactive State**: Vue 3 Composition API for reactive data management
- **Composables**: Reusable state logic with TypeScript support
- **Error Handling**: Centralized error state management

### 4. Error Handling
- **Global Error Handling**: Centralized error management
- **Error Boundaries**: React error boundary pattern for component error isolation
- **User Feedback**: User-friendly error messages and logging

## Styling and UI

### Design System
- **Tailwind CSS**: Utility-first CSS framework
- **Responsive Design**: Mobile-first approach
- **Accessibility**: WCAG 2.1 compliance
- **Dark Mode**: Theme switching support

## Testing Strategy

### Test Structure
```
e2e/
├── tsconfig.json
└── vue.spec.ts          # End-to-end tests
```

### Testing Patterns
- **Component Testing**: Vue Test Utils for component tests
- **E2E Testing**: Playwright for end-to-end tests
- **Unit Testing**: Vitest for unit tests
- **Integration Testing**: API integration tests

 