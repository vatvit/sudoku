# Performance Guidelines

## Backend Performance (Symfony/PHP)

### Database Query Optimization

**Use Proper Indexing:**
```sql
-- Game instance lookups
CREATE INDEX idx_game_id ON game_instances (game_id);
CREATE INDEX idx_status ON game_instances (status);
CREATE INDEX idx_created_at ON game_instances (created_at);

-- Game actions for history
CREATE INDEX idx_game_action_game_id ON game_actions (game_id);
CREATE INDEX idx_game_action_timestamp ON game_actions (timestamp);
CREATE COMPOSITE INDEX idx_game_action_lookup ON game_actions (game_id, timestamp);
```

**Optimize Doctrine Queries:**
```php
// ❌ Bad: N+1 query problem
$games = $this->gameRepository->findAll();
foreach ($games as $game) {
    echo $game->getUser()->getName(); // Triggers additional query
}

// ✅ Good: Use joins to fetch related data
$games = $this->entityManager->createQueryBuilder()
    ->select('g', 'u')
    ->from(GameInstance::class, 'g')
    ->leftJoin('g.user', 'u')
    ->getQuery()
    ->getResult();
```

**Use Query Builder for Complex Queries:**
```php
// ✅ Optimized query for game statistics
public function getGameStatistics(string $userId): array
{
    return $this->entityManager->createQueryBuilder()
        ->select('
            COUNT(g.id) as total_games,
            COUNT(CASE WHEN g.status = :completed THEN 1 END) as completed_games,
            AVG(g.completion_time) as avg_completion_time
        ')
        ->from(GameInstance::class, 'g')
        ->where('g.userId = :userId')
        ->setParameter('userId', $userId)
        ->setParameter('completed', GameStatus::COMPLETED)
        ->getQuery()
        ->getSingleResult();
}
```

**Implement Pagination:**
```php
// ✅ Paginated results for large datasets
public function getGameHistory(string $userId, int $page = 1, int $limit = 20): array
{
    $offset = ($page - 1) * $limit;
    
    return $this->entityManager->createQueryBuilder()
        ->select('g')
        ->from(GameInstance::class, 'g')
        ->where('g.userId = :userId')
        ->orderBy('g.createdAt', 'DESC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getResult();
}
```

### Caching Strategies

**Redis Cache Implementation:**
```php
class GameInstanceService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'game|instance|sudoku|';
    
    public function getCachedGameInstance(string $gameId): ?SudokuGameInstanceDto
    {
        $cacheKey = self::CACHE_PREFIX . $gameId;
        $cacheItem = $this->cache->getItem($cacheKey);
        
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
        
        return null;
    }
    
    public function cacheGameInstance(string $gameId, SudokuGameInstanceDto $dto): void
    {
        $cacheKey = self::CACHE_PREFIX . $gameId;
        $cacheItem = $this->cache->getItem($cacheKey);
        
        $cacheItem->set($dto);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        
        // Tag for easier invalidation
        $cacheItem->tag(['game_instances', 'game_' . $gameId]);
        
        $this->cache->save($cacheItem);
    }
}
```

**HTTP Response Caching:**
```php
// ✅ Cache static configuration data
#[Route('/api/config', methods: ['GET'])]
public function getConfig(): JsonResponse
{
    $response = $this->json($this->configService->getPublicConfig());
    
    // Cache for 1 hour
    $response->setMaxAge(3600);
    $response->setPublic();
    
    return $response;
}

// ✅ No cache for dynamic game data
#[Route('/api/games/sudoku/instances/{gameId}', methods: ['GET'])]
public function getInstance(string $gameId): JsonResponse
{
    $response = $this->json($this->gameService->getInstance($gameId));
    
    // Prevent caching of dynamic data
    $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
    
    return $response;
}
```

**Cache Invalidation Strategy:**
```php
class GameActionService
{
    public function processAction(string $gameId, ActionDto $action): void
    {
        // Process the action
        $this->gameService->applyAction($gameId, $action);
        
        // Invalidate related caches
        $this->cache->invalidateTags(['game_' . $gameId]);
        
        // Update cache with new state
        $updatedGame = $this->gameService->getInstance($gameId);
        $this->cacheGameInstance($gameId, $updatedGame);
    }
}
```

### Memory Usage Optimization

**Use Generators for Large Datasets:**
```php
// ✅ Memory-efficient processing of large datasets
public function processGameHistory(string $userId): \Generator
{
    $query = $this->entityManager->createQuery(
        'SELECT g FROM App\Entity\GameInstance g WHERE g.userId = :userId'
    );
    $query->setParameter('userId', $userId);
    
    foreach ($query->toIterable() as $game) {
        yield $game;
    }
}

// Usage
foreach ($this->gameService->processGameHistory($userId) as $game) {
    // Process one game at a time without loading all into memory
    $this->processGame($game);
}
```

**Clear Entity Manager Periodically:**
```php
// ✅ Prevent memory leaks in long-running processes
public function processBatchActions(array $actions): void
{
    foreach ($actions as $index => $action) {
        $this->processAction($action);
        
        // Clear entity manager every 100 operations
        if ($index % 100 === 0) {
            $this->entityManager->clear();
        }
    }
}
```

### API Response Optimization

**Minimize Response Payload:**
```php
// ✅ Return only necessary data
class GameInstanceResponseDto
{
    public function __construct(
        public readonly string $gameId,
        public readonly array $grid,           // Only current state
        public readonly string $status,
        // Don't include: full history, internal metadata, etc.
    ) {}
}
```

**Use Compression:**
```php
// Enable gzip compression in web server configuration
// Or use Symfony's compression middleware
```

## Frontend Performance (Vue.js)

### Component Optimization

**Use Computed Properties for Expensive Calculations:**
```typescript
// ✅ Cached computed property
const cellClasses = computed(() => {
  const classes: string[] = []
  
  if (isSelected.value) classes.push('selected')
  if (isHovered.value) classes.push('hovered')
  if (props.cell.protected) classes.push('protected')
  if (store.mistakes.has(props.cell.coords)) classes.push('mistake')
  
  return classes
})

// ❌ Bad: Recalculated on every render
function getCellClasses(): string[] {
  // Expensive calculation on every render
}
```

**Optimize Component Re-renders:**
```typescript
// ✅ Use reactive references efficiently
const selectedCell = ref<Cell | null>(null)
const hoveredCells = ref<Set<string>>(new Set())

// ✅ Batch updates to prevent multiple re-renders
function selectCellAndHighlight(coords: string, value: number): void {
  // Batch these updates
  nextTick(() => {
    selectedCell.value = findCellByCoords(coords)
    highlightedValue.value = value
  })
}
```

**Lazy Load Components:**
```typescript
// ✅ Lazy load heavy components
const GameStatistics = defineAsyncComponent(() => import('./GameStatistics.vue'))
const GameHistory = defineAsyncComponent(() => import('./GameHistory.vue'))

// Use with loading and error states
const GameStatistics = defineAsyncComponent({
  loader: () => import('./GameStatistics.vue'),
  loadingComponent: LoadingSpinner,
  errorComponent: ErrorComponent,
  delay: 200,
  timeout: 3000
})
```

### State Management Optimization

**Optimize Pinia Store Updates:**
```typescript
// ✅ Efficient state updates
export const useGameStore = defineStore('game', () => {
  const gameState = ref<GameState>({
    grid: [],
    selectedCell: null,
    mistakes: new Set()
  })
  
  // ✅ Batch related updates
  function updateCellAndValidate(coords: string, value: number): void {
    const newState = { ...gameState.value }
    newState.grid = updateGridCell(newState.grid, coords, value)
    newState.mistakes = validateGrid(newState.grid)
    
    gameState.value = newState
  }
  
  // ✅ Use computed for derived state
  const hasErrors = computed(() => gameState.value.mistakes.size > 0)
  const completedCells = computed(() => 
    gameState.value.grid.flat().filter(cell => cell.value > 0).length
  )
  
  return { gameState, hasErrors, completedCells, updateCellAndValidate }
})
```

**Minimize Reactive Overhead:**
```typescript
// ✅ Use shallowRef for large objects that don't need deep reactivity
const gameGrid = shallowRef<Cell[][]>([])

// ✅ Use readonly for data that shouldn't change
const gameConfig = readonly({
  gridSize: 9,
  difficulty: 'medium'
})
```

### Bundle Size Optimization

**Code Splitting:**
```typescript
// ✅ Route-based code splitting
const routes = [
  {
    path: '/game',
    component: () => import('./views/GameView.vue')
  },
  {
    path: '/statistics',
    component: () => import('./views/StatisticsView.vue')
  }
]
```

**Tree Shaking:**
```typescript
// ✅ Import only what you need
import { ref, computed, nextTick } from 'vue'

// ❌ Bad: Imports entire library
import * as Vue from 'vue'
```

**Optimize Dependencies:**
```typescript
// ✅ Use lighter alternatives when possible
// Instead of moment.js, use date-fns or native Date
import { format } from 'date-fns'

// ✅ Use dynamic imports for heavy libraries
async function loadChartLibrary() {
  const { Chart } = await import('chart.js')
  return Chart
}
```

### API Call Optimization

**Request Deduplication:**
```typescript
// ✅ Prevent duplicate API calls
class ApiService {
  private pendingRequests = new Map<string, Promise<any>>()
  
  async getGameInstance(gameId: string): Promise<GameInstance> {
    const cacheKey = `game-${gameId}`
    
    if (this.pendingRequests.has(cacheKey)) {
      return this.pendingRequests.get(cacheKey)!
    }
    
    const promise = this.api.games.getGameSudokuInstance(gameId)
    this.pendingRequests.set(cacheKey, promise)
    
    try {
      const result = await promise
      return result.data
    } finally {
      this.pendingRequests.delete(cacheKey)
    }
  }
}
```

**Implement Request Caching:**
```typescript
// ✅ Cache API responses
class CachedApiService {
  private cache = new Map<string, { data: any; timestamp: number }>()
  private readonly CACHE_TTL = 5 * 60 * 1000 // 5 minutes
  
  async getCachedData<T>(key: string, fetcher: () => Promise<T>): Promise<T> {
    const cached = this.cache.get(key)
    const now = Date.now()
    
    if (cached && (now - cached.timestamp) < this.CACHE_TTL) {
      return cached.data
    }
    
    const data = await fetcher()
    this.cache.set(key, { data, timestamp: now })
    
    return data
  }
}
```

**Optimize Real-time Updates:**
```typescript
// ✅ Efficient Mercure subscription management
class MercureService {
  private connections = new Map<string, EventSource>()
  
  subscribe(gameId: string, callback: (data: any) => void): void {
    const topic = `game/${gameId}`
    
    if (this.connections.has(topic)) {
      return // Already subscribed
    }
    
    const url = new URL(this.hubUrl)
    url.searchParams.append('topic', topic)
    
    const eventSource = new EventSource(url.toString())
    eventSource.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data)
        callback(data)
      } catch (error) {
        console.error('Failed to parse Mercure message:', error)
      }
    }
    
    this.connections.set(topic, eventSource)
  }
  
  unsubscribe(gameId: string): void {
    const topic = `game/${gameId}`
    const connection = this.connections.get(topic)
    
    if (connection) {
      connection.close()
      this.connections.delete(topic)
    }
  }
}
```

## Monitoring and Metrics

### Key Performance Indicators

**Backend Metrics:**
- API response time (target: < 200ms for game actions)
- Database query time (target: < 50ms per query)
- Cache hit rate (target: > 80%)
- Memory usage (target: < 256MB per request)
- CPU usage (target: < 70% average)

**Frontend Metrics:**
- First Contentful Paint (target: < 1.5s)
- Largest Contentful Paint (target: < 2.5s)
- Time to Interactive (target: < 3.5s)
- Bundle size (target: < 500KB gzipped)
- JavaScript execution time (target: < 100ms)

### Performance Testing

**Backend Load Testing:**
```bash
# Use Apache Bench for simple load testing
ab -n 1000 -c 10 http://localhost/api/games/sudoku/instances

# Use Artillery for more complex scenarios
artillery run artillery-config.yml
```

**Frontend Performance Testing:**
```typescript
// Use Lighthouse CI for automated performance testing
// lighthouse-ci.json
{
  "ci": {
    "collect": {
      "url": ["http://localhost:5173"],
      "numberOfRuns": 3
    },
    "assert": {
      "assertions": {
        "categories:performance": ["error", {"minScore": 0.9}],
        "categories:accessibility": ["error", {"minScore": 0.9}]
      }
    }
  }
}
```

### Monitoring Tools Setup

**Backend Monitoring:**
```php
// Add performance logging
class PerformanceMiddleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $handler->handle($request);
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        $memoryUsage = memory_get_usage() - $startMemory;
        
        $this->logger->info('Request performance', [
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
            'execution_time_ms' => $executionTime,
            'memory_usage_bytes' => $memoryUsage,
            'response_status' => $response->getStatusCode()
        ]);
        
        return $response;
    }
}
```

**Frontend Monitoring:**
```typescript
// Performance monitoring with Web Vitals
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals'

function sendToAnalytics(metric: any) {
  // Send to your analytics service
  console.log(metric)
}

getCLS(sendToAnalytics)
getFID(sendToAnalytics)
getFCP(sendToAnalytics)
getLCP(sendToAnalytics)
getTTFB(sendToAnalytics)
```

### Performance Optimization Checklist

**Backend Checklist:**
- [ ] Database queries are optimized with proper indexes
- [ ] Caching is implemented for frequently accessed data
- [ ] API responses are minimized and compressed
- [ ] Memory usage is monitored and optimized
- [ ] Database connection pooling is configured
- [ ] Slow query logging is enabled

**Frontend Checklist:**
- [ ] Components are optimized for minimal re-renders
- [ ] Bundle size is minimized with code splitting
- [ ] Images are optimized and properly sized
- [ ] API calls are deduplicated and cached
- [ ] Real-time updates are efficiently managed
- [ ] Performance metrics are monitored

These performance guidelines ensure optimal user experience while maintaining system scalability and reliability.