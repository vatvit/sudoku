# Development Workflows

## Feature Development Process

### 1. Domain Modeling Approach

**Step 1: Analyze Business Requirements**
```bash
# Create feature branch
git checkout -b feature/sudoku-undo-redo

# Document requirements in issue or feature spec
```

**Step 2: Domain Layer Design**
```php
// 1. Define domain entities and value objects
namespace App\Domain\Sudoku\ValueObject;

class GameAction
{
    public function __construct(
        public readonly ActionType $type,
        public readonly CellCoordinates $coords,
        public readonly ?int $value,
        public readonly ?array $notes,
        public readonly \DateTimeImmutable $timestamp
    ) {}
}

// 2. Create domain services
namespace App\Domain\Sudoku\Service;

class ActionHistoryService
{
    public function recordAction(GameAction $action): void
    {
        // Domain logic for recording actions
    }
    
    public function undoLastAction(string $gameId): ?GameAction
    {
        // Domain logic for undo
    }
}
```

**Step 3: Write Domain Tests First**
```php
// tests/Unit/Domain/Sudoku/Service/ActionHistoryServiceTest.php
class ActionHistoryServiceTest extends TestCase
{
    public function testRecordAction(): void
    {
        // Arrange
        $service = new ActionHistoryService();
        $action = new GameAction(/* ... */);
        
        // Act
        $service->recordAction($action);
        
        // Assert
        $this->assertTrue($service->hasActions());
    }
}
```

### 2. Test-First Development Steps

**Step 1: Write Failing Tests**
```bash
# Run tests to see failures
composer test-unit
```

**Step 2: Implement Minimum Code**
```php
// Implement just enough to make tests pass
class ActionHistoryService
{
    private array $actions = [];
    
    public function recordAction(GameAction $action): void
    {
        $this->actions[] = $action;
    }
    
    public function hasActions(): bool
    {
        return !empty($this->actions);
    }
}
```

**Step 3: Refactor and Improve**
```bash
# Run tests after each refactor
composer test-unit
composer phpstan
composer cs-check
```

### 3. API Design and Documentation

**Step 1: Define API Contract**
```php
// src/Interface/Controller/Sudoku/Dto/UndoActionRequestDto.php
class UndoActionRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Game ID is required')]
        #[Assert\Uuid(message: 'Game ID must be a valid UUID')]
        public readonly string $gameId
    ) {}
}

// src/Interface/Controller/Sudoku/Dto/UndoActionResponseDto.php
class UndoActionResponseDto
{
    public function __construct(
        public readonly bool $success,
        public readonly ?array $previousState,
        public readonly ?string $message
    ) {}
}
```

**Step 2: Create Controller with OpenAPI Documentation**
```php
#[Route(
    '/api/games/sudoku/instances/{gameId}/undo',
    name: 'undo-game-sudoku-instance-action',
    methods: ['POST']
)]
#[OA\Response(
    response: 200,
    description: 'Action undone successfully',
    content: new OA\JsonContent(ref: new Model(type: UndoActionResponseDto::class))
)]
#[OA\Response(
    response: 404,
    description: 'Game not found or no actions to undo'
)]
#[OA\Tag(name: 'game-sudoku-instance-actions')]
public function undo(
    string $gameId,
    MessageBusInterface $messageBus
): JsonResponse {
    $command = new UndoGameActionCommand($gameId);
    $result = $messageBus->dispatch($command);
    
    return $this->json($result);
}
```

**Step 3: Generate and Update API Documentation**
```bash
# Generate OpenAPI specification
composer openapi-generate

# Update frontend API client
cd src/clientAppVue
npm run openapi:generate
```

### 4. Frontend Integration Process

**Step 1: Update TypeScript Types**
```typescript
// src/types/GameAction.ts
export interface GameAction {
  type: 'SET_VALUE' | 'SET_NOTE' | 'CLEAR_CELL'
  coords: string
  value?: number
  notes?: number[]
  timestamp: string
}

export interface UndoRedoState {
  canUndo: boolean
  canRedo: boolean
  actionHistory: GameAction[]
}
```

**Step 2: Update Store with New Actions**
```typescript
// src/stores/GameStore.ts
export const useGameStore = defineStore('game', () => {
  const actionHistory = ref<GameAction[]>([])
  const currentActionIndex = ref(-1)
  
  const canUndo = computed(() => currentActionIndex.value >= 0)
  const canRedo = computed(() => currentActionIndex.value < actionHistory.value.length - 1)
  
  async function undoAction(): Promise<void> {
    if (!canUndo.value) return
    
    try {
      const response = await apiService.undoAction(gameId.value)
      if (response.success) {
        currentActionIndex.value--
        // Update game state with previous state
      }
    } catch (error) {
      handleError(error)
    }
  }
  
  return {
    actionHistory,
    canUndo,
    canRedo,
    undoAction
  }
})
```

**Step 3: Create UI Components**
```vue
<!-- src/components/Sudoku/UndoRedoControls.vue -->
<script setup lang="ts">
import { useGameStore } from '@/stores/GameStore'

const store = useGameStore()

async function handleUndo(): Promise<void> {
  await store.undoAction()
}

async function handleRedo(): Promise<void> {
  await store.redoAction()
}
</script>

<template>
  <div class="undo-redo-controls">
    <button 
      :disabled="!store.canUndo"
      @click="handleUndo"
      class="btn btn-secondary"
    >
      Undo
    </button>
    <button 
      :disabled="!store.canRedo"
      @click="handleRedo"
      class="btn btn-secondary"
    >
      Redo
    </button>
  </div>
</template>

<style scoped>
.undo-redo-controls {
  display: flex;
  gap: 8px;
  margin: 16px 0;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
```

### 5. Testing and Validation Steps

**Step 1: Backend Testing**
```bash
# Run unit tests
composer test-unit

# Run integration tests
composer test-acceptance

# Run all quality checks
composer check-all
```

**Step 2: Frontend Testing**
```bash
# Run unit tests
npm run test:unit

# Run E2E tests
npm run test:e2e

# Type checking
npm run type-check
```

**Step 3: Manual Testing Checklist**
- [ ] Feature works in development environment
- [ ] API endpoints return correct responses
- [ ] Frontend UI updates correctly
- [ ] Error handling works as expected
- [ ] Performance is acceptable
- [ ] Accessibility requirements met

## Bug Fix Workflow

### 1. Issue Reproduction Steps

**Step 1: Create Reproduction Script**
```php
// scripts/reproduce-bug.php
<?php
require_once 'vendor/autoload.php';

// Reproduce the exact conditions that cause the bug
$gameService = new GameInstanceService();
$gameId = $gameService->createGame(9, 'medium');

// Steps to reproduce
$action1 = new ActionDto('SET_VALUE', '1:1', 5);
$gameService->processAction($gameId, $action1);

$action2 = new ActionDto('SET_VALUE', '1:2', 5); // This should cause validation error
$result = $gameService->processAction($gameId, $action2);

echo "Result: " . json_encode($result) . "\n";
```

**Step 2: Create Failing Test**
```php
public function testDuplicateValueInRowShouldBeRejected(): void
{
    // Arrange
    $gameId = $this->createTestGame();
    $action1 = new ActionDto('SET_VALUE', '1:1', 5);
    $action2 = new ActionDto('SET_VALUE', '1:2', 5);
    
    // Act
    $this->gameService->processAction($gameId, $action1);
    
    // Assert
    $this->expectException(ValidationException::class);
    $this->gameService->processAction($gameId, $action2);
}
```

### 2. Root Cause Analysis Approach

**Step 1: Debug with Logging**
```php
// Add debug logging
use Psr\Log\LoggerInterface;

class GameValidationService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function validateAction(ActionDto $action, array $gameState): bool
    {
        $this->logger->debug('Validating action', [
            'action' => $action,
            'gameState' => $gameState
        ]);
        
        // Validation logic
        $isValid = $this->checkRowConstraints($action, $gameState);
        
        $this->logger->debug('Validation result', ['isValid' => $isValid]);
        
        return $isValid;
    }
}
```

**Step 2: Use Profiler for Performance Issues**
```bash
# Enable Symfony profiler
APP_ENV=dev

# Access profiler at /_profiler after making requests
```

**Step 3: Database Query Analysis**
```bash
# Enable query logging in development
# Check var/log/dev.log for slow queries
tail -f var/log/dev.log | grep "doctrine"
```

### 3. Fix Implementation Strategy

**Step 1: Implement Minimal Fix**
```php
// Fix the specific issue without changing too much
class GameValidationService
{
    private function checkRowConstraints(ActionDto $action, array $gameState): bool
    {
        $coords = CellCoordinates::fromString($action->coords);
        $row = $gameState['cells'][$coords->row - 1];
        
        foreach ($row as $cell) {
            if ($cell['value'] === $action->value && $cell['coords'] !== $action->coords) {
                return false; // Duplicate value in row
            }
        }
        
        return true;
    }
}
```

**Step 2: Add Regression Tests**
```php
public function testRowValidationAfterBugFix(): void
{
    // Test various scenarios to prevent regression
    $testCases = [
        ['1:1', 5, '1:2', 5, false], // Same row, same value
        ['1:1', 5, '2:1', 5, true],  // Same column, same value (should be valid for row check)
        ['1:1', 5, '1:3', 6, true],  // Same row, different value
    ];
    
    foreach ($testCases as [$coords1, $value1, $coords2, $value2, $expected]) {
        // Test each case
    }
}
```

### 4. Regression Testing Process

**Step 1: Run Full Test Suite**
```bash
# Backend tests
composer test
composer phpstan
composer cs-check

# Frontend tests
npm run test:unit
npm run test:e2e
npm run type-check
npm run lint
```

**Step 2: Manual Testing**
```bash
# Test the specific bug scenario
php scripts/reproduce-bug.php

# Test related functionality
# - Game creation
# - Action processing
# - Validation rules
```

## Code Review Guidelines

### What to Look For in Reviews

**Architecture and Design:**
- [ ] Follows clean architecture principles
- [ ] Proper layer separation (Domain, Application, Infrastructure)
- [ ] CQRS pattern correctly implemented
- [ ] Dependency injection used properly

**Code Quality:**
- [ ] PSR-12 coding standards (PHP)
- [ ] TypeScript strict mode compliance (Frontend)
- [ ] Proper error handling
- [ ] No code duplication
- [ ] Clear variable and method names

**Testing:**
- [ ] Unit tests cover new functionality
- [ ] Integration tests for API endpoints
- [ ] Edge cases are tested
- [ ] Mocks are used appropriately

**Security:**
- [ ] Input validation is present
- [ ] No SQL injection vulnerabilities
- [ ] Proper authentication/authorization
- [ ] No sensitive data in logs

**Performance:**
- [ ] Database queries are optimized
- [ ] Caching is used where appropriate
- [ ] No N+1 query problems
- [ ] Frontend bundle size impact

### Common Issues to Catch

**Backend Issues:**
```php
// ❌ Bad: Direct database access in controller
class GameController extends AbstractController
{
    public function getGame(EntityManagerInterface $em): JsonResponse
    {
        $game = $em->getRepository(Game::class)->find($id);
        return $this->json($game);
    }
}

// ✅ Good: Use application service
class GameController extends AbstractController
{
    public function getGame(
        string $gameId,
        MessageBusInterface $messageBus
    ): JsonResponse {
        $query = new GetGameQuery($gameId);
        $result = $messageBus->dispatch($query);
        return $this->json($result);
    }
}
```

**Frontend Issues:**
```typescript
// ❌ Bad: Direct DOM manipulation
function updateCell(coords: string, value: number): void {
  document.querySelector(`[data-coords="${coords}"]`).textContent = value.toString()
}

// ✅ Good: Use reactive state
function updateCell(coords: string, value: number): void {
  const store = useGameStore()
  store.setCellValue(coords, value)
}
```

### Performance Considerations

**Database Optimization:**
- Use proper indexes
- Avoid N+1 queries
- Use query builders for complex queries
- Implement pagination for large datasets

**Caching Strategy:**
- Cache frequently accessed data
- Use appropriate cache TTL
- Implement cache invalidation
- Monitor cache hit rates

**Frontend Performance:**
- Lazy load components
- Optimize bundle size
- Use proper image formats
- Implement virtual scrolling for large lists

## Deployment Workflow

### Local to Staging

**Step 1: Prepare for Deployment**
```bash
# Run all tests
composer check-all
npm run test:unit && npm run test:e2e

# Build frontend
npm run build

# Generate API documentation
composer openapi-generate
```

**Step 2: Deploy to Staging**
```bash
# Deploy infrastructure
cd infra/staging
terraform plan
terraform apply

# Deploy application
# (This would typically be handled by CI/CD)
```

**Step 3: Staging Validation**
```bash
# Run smoke tests against staging
curl -X POST https://staging-api.example.com/api/games/sudoku/instances \
  -H "Content-Type: application/json" \
  -d '{"size": 9, "difficulty": "medium"}'

# Test frontend
open https://staging.example.com
```

### Production Deployment

**Step 1: Final Checks**
- [ ] All tests passing
- [ ] Code review approved
- [ ] Security scan completed
- [ ] Performance benchmarks met
- [ ] Documentation updated

**Step 2: Blue-Green Deployment**
```bash
# Deploy to green environment
terraform apply -var="environment=green"

# Run health checks
curl https://green-api.example.com/health

# Switch traffic
terraform apply -var="active_environment=green"
```

**Step 3: Post-Deployment Monitoring**
- Monitor application logs
- Check performance metrics
- Verify real-time features work
- Monitor error rates

This workflow ensures consistent, high-quality development practices across the entire team and project lifecycle.