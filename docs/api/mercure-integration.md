# Mercure Integration

## 📋 Purpose of This File

**For AI Assistants**: This file describes the Mercure real-time communication integration, including server-sent events, topic-based messaging, authentication, and real-time synchronization patterns. Use this when you need to understand how real-time communication works, how to implement server-sent events, or how to handle live updates in the application.

**For Developers**: This document explains the real-time communication implementation using Mercure. Use it to understand how server-sent events work, how to subscribe to real-time updates, and how to implement live synchronization features.

**How to Use**:
- Reference this for real-time communication implementation
- Use it to understand Mercure hub configuration and usage
- Follow the patterns for implementing server-sent events
- Use the examples to understand subscription and publishing patterns

---

# Mercure Integration

## Overview

Mercure is used for real-time communication in the Sudoku application, enabling live game updates and multi-player synchronization. This document details the Mercure integration architecture, configuration, and usage patterns.

## Architecture

### Mercure Hub Setup

The application uses a dedicated Mercure hub for real-time messaging:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Backend App   │    │   Mercure Hub   │    │   Frontend Apps │
│   (Publisher)   │───▶│   (Caddy)       │───▶│   (Subscribers) │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Components

1. **Mercure Hub**: Caddy server with Mercure module
2. **Publishers**: Backend application publishing events
3. **Subscribers**: Frontend applications receiving updates

## Configuration

### Environment Variables

**Local Development:**
```env
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLISHER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
MERCURE_SUBSCRIBER_JWT_KEY=!ChangeThisMercureHubJWTSecretKey!
MERCURE_PUBLIC_URL=http://localhost/.well-known/mercure
```

**Staging Environment:**
```env
MERCURE_URL=https://api.example.com/.well-known/mercure
MERCURE_PUBLISHER_JWT_KEY=${MERCURE_JWT_KEY}
MERCURE_SUBSCRIBER_JWT_KEY=${MERCURE_JWT_KEY}
MERCURE_PUBLIC_URL=https://api.example.com/.well-known/mercure
```

### Caddy Configuration

**Local Development (`infra/docker/mercure/caddy/Caddyfile.dev`):**
```caddy
{
    auto_https off
    http_port 80
}

localhost

route {
    mercure {
        transport_url bolt://mercure.db
        publisher_jwt {env.MERCURE_PUBLISHER_JWT_KEY} {env.MERCURE_PUBLISHER_JWT_ALG}
        subscriber_jwt {env.MERCURE_SUBSCRIBER_JWT_KEY} {env.MERCURE_SUBSCRIBER_JWT_ALG}
    }
}
```

**Production (`infra/docker/mercure/caddy/Caddyfile`):**
```caddy
{
    auto_https off
    http_port 80
}

{$SERVER_NAME:localhost}

route {
    mercure {
        transport_url {$MERCURE_TRANSPORT_URL:bolt://mercure.db}
        publisher_jwt {env.MERCURE_PUBLISHER_JWT_KEY} {env.MERCURE_PUBLISHER_JWT_ALG}
        subscriber_jwt {env.MERCURE_SUBSCRIBER_JWT_KEY} {env.MERCURE_SUBSCRIBER_JWT_ALG}
    }
}
```

## Publishing Events

### Backend Integration

The backend uses Mercure services for publishing real-time events. Implementation details are available in the source code:

- **Publisher Service**: [src/backendApp/src/Application/Service/Mercure/Publisher.php](../src/backendApp/src/Application/Service/Mercure/Publisher.php)
- **Factory Service**: [src/backendApp/src/Application/Service/Mercure/Factory.php](../src/backendApp/src/Application/Service/Mercure/Factory.php)

### Event Types

The application publishes various event types for real-time game synchronization:

- **Game Update Events**: Player actions and state changes
- **Game State Events**: Complete game state synchronization  
- **Game Completion Events**: Game finish notifications

Event structures follow JSON format with type identification, game context, and timestamp metadata.

## Subscribing to Events

### Frontend Integration

Frontend applications subscribe to Mercure events using the EventSource API. Implementation examples are available in the source code:

- **Vue.js Component**: [src/clientAppVue/src/components/MercureSubscribe.vue](../src/clientAppVue/src/components/MercureSubscribe.vue)
- **Event Module**: [src/clientAppVue/src/modules/event/index.ts](../src/clientAppVue/src/modules/event/index.ts)

### Subscription Pattern

1. **Topic Subscription**: Subscribe to game-specific topics
2. **EventSource Management**: Handle connection lifecycle
3. **Message Processing**: Parse and handle incoming events
4. **Error Handling**: Implement reconnection logic

## Topic Structure

### Topic Naming Convention

Topics follow a hierarchical structure for efficient routing:

```
/api/games/sudoku/instances/{gameId}
```

**Examples:**
- `/api/games/sudoku/instances/550e8400-e29b-41d4-a716-446655440000`
- `/api/games/sudoku/instances/123e4567-e89b-12d3-a456-426614174000`

### Topic Patterns

- **Game-specific**: All game updates for a specific instance
- **User-specific**: Future implementation for user notifications
- **System-wide**: Future implementation for system announcements

## Security

### JWT Authentication

**Publisher JWT:**
- Used by backend to publish events
- Contains publisher permissions
- Signed with secret key

**Subscriber JWT:**
- Used by frontend to subscribe to events
- Contains subscription permissions
- Signed with secret key



### CORS Configuration

CORS settings are configured in the Caddy Mercure module to allow cross-origin requests for development and production environments.

## Performance & Scalability

### Connection Management

- **Connection Pooling**: Reuse connections when possible
- **Reconnection Logic**: Automatic reconnection on connection loss
- **Connection Limits**: Monitor and limit concurrent connections

### Message Optimization

- **Message Size**: Keep messages small and focused
- **Batch Updates**: Combine multiple updates when possible
- **Compression**: Enable gzip compression for large messages

### Monitoring

- **Connection Metrics**: Track active connections
- **Message Volume**: Monitor message throughput
- **Error Rates**: Track connection and message errors

## Troubleshooting

### Common Issues

**Connection Refused:**
- Check Mercure hub is running
- Verify network connectivity
- Check firewall settings

**Authentication Errors:**
- Verify JWT tokens are valid
- Check token expiration
- Ensure correct signing keys

**Message Not Received:**
- Verify topic subscription
- Check message format
- Monitor hub logs

### Debug Tools

- **Mercure UI**: Access via `http://localhost/.well-known/mercure/ui/` for monitoring
- **Health Check**: Available at `http://localhost/.well-known/mercure/healthz`
- **Manual Publishing**: Use debug endpoint for testing

 