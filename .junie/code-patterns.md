# Code Patterns and Conventions

## Backend Patterns (Symfony/PHP)

### CQRS Implementation Pattern

**Command Pattern:**
```php
<?php
namespace App\Application\CQRS\Command\Sudoku;

use App\Application\CQRS\Command\CommandInterface;

class CreateGameInstanceCommand implements CommandInterface
{
    public function __construct(
        public readonly int $size = 9,
        public readonly string $difficulty = 'medium'
    ) {}
}
```

**Command Handler Pattern:**
```php
<?php
namespace App\Application\CQRS\Command\Sudoku;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateGameInstanceCommandHandler
{
    public function __construct(
        private readonly GameInstanceService $gameInstanceService,
        private readonly CacheInterface $cache
    ) {}

    public function __invoke(CreateGameInstanceCommand $command): GameInstanceDto
    {
        // Implementation
    }
}
```

**Query Pattern:**
```php
<?php
namespace App\Application\CQRS\Query\Sudoku;

use App\Application\CQRS\Query\QueryInterface;

class GetGameInstanceQuery implements QueryInterface
{
    public function __construct(
        public readonly string $gameId
    ) {}
}
```

### Controller Pattern

**REST Controller Structure:**
```php
<?php
namespace App\Interface\Controller\Sudoku;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class InstanceController extends AbstractController
{
    #[Route(
        '/api/games/sudoku/instances',
        name: 'create-game-sudoku-instance',
        methods: ['POST']
    )]
    #[OA\Response(
        response: 200,
        description: 'Game instance created successfully',
        content: new OA\JsonContent(ref: new Model(type: InstanceCreateResponseDto::class))
    )]
    #[OA\Tag(name: 'game-sudoku')]
    public function create(
        #[MapRequestPayload] InstanceCreateRequestDto $requestDto,
        MessageBusInterface $messageBus
    ): JsonResponse {
        $command = new CreateGameInstanceCommand(
            size: $requestDto->size,
            difficulty: $requestDto->difficulty
        );
        
        $result = $messageBus->dispatch($command);
        
        return $this->json($result);
    }
}
```

### DTO Pattern with Validation

**Request DTO:**
```php
<?php
namespace App\Interface\Controller\Sudoku\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class InstanceCreateRequestDto
{
    public function __construct(
        #[Assert\Choice(choices: [4, 9, 16], message: 'Grid size must be 4, 9, or 16')]
        public readonly int $size = 9,
        
        #[Assert\Choice(choices: ['easy', 'medium', 'hard'], message: 'Invalid difficulty level')]
        public readonly string $difficulty = 'medium'
    ) {}
}
```

**Response DTO:**
```php
<?php
namespace App\Interface\Controller\Sudoku\Dto;

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

### Mapper Pattern

**Entity to DTO Mapping:**
```php
<?php
namespace App\Application\Service\Sudoku\Mapper;

class SudokuGameInstanceEntityToDtoMapper
{
    public function map(SudokuGameInstanceEntity $entity): SudokuGameInstanceDto
    {
        return new SudokuGameInstanceDto(
            gameId: $entity->getGameId(),
            grid: $this->mapGrid($entity->getGrid()),
            cellGroups: $this->mapCellGroups($entity->getCellGroups()),
            status: $entity->getStatus()->value
        );
    }

    private function mapGrid(array $grid): array
    {
        return array_map(
            fn(array $row) => array_map(
                fn(CellEntity $cell) => $this->mapCell($cell),
                $row
            ),
            $grid
        );
    }
}
```

### Domain Service Pattern

**Domain Service Structure:**
```php
<?php
namespace App\Domain\Sudoku\Service;

class GridGenerator
{
    public function __construct(
        private readonly GridShuffler $gridShuffler
    ) {}

    public function generate(int $size = 9): array
    {
        if (!$this->isValidGridSize($size)) {
            throw new \InvalidArgumentException('Grid size must be a perfect square number');
        }

        $grid = $this->generateBaseGrid($size);
        return $this->gridShuffler->shuffle($grid, 10);
    }

    private function isValidGridSize(int $size): bool
    {
        $sqrt = (int)sqrt($size);
        return $sqrt * $sqrt === $size && $size > 0;
    }
}
```

### Cache Pattern

**Cache Key Strategy:**
```php
<?php
namespace App\Application\Service\Sudoku;

class GameInstanceService
{
    private const CACHE_PREFIX = 'game|instance|sudoku|';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly CacheInterface $cache
    ) {}

    public function getCachedGameInstance(string $gameId): ?SudokuGameInstanceDto
    {
        $cacheKey = self::CACHE_PREFIX . $gameId;
        $cacheItem = $this->cache->getItem($cacheKey);
        
        return $cacheItem->isHit() ? $cacheItem->get() : null;
    }

    public function cacheGameInstance(string $gameId, SudokuGameInstanceDto $dto): void
    {
        $cacheKey = self::CACHE_PREFIX . $gameId;
        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($dto);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        
        $this->cache->save($cacheItem);
    }
}
```

## Frontend Patterns (Vue.js/TypeScript)

### Composition API Pattern

**Component Structure:**
```vue
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Cell } from '@/types/Cell'
import type { GameStore } from '@/stores/GameStore'

// Props definition with TypeScript
interface Props {
  cell: Cell
  store: GameStore
}

const props = defineProps<Props>()

// Reactive state
const isHovered = ref(false)
const isSelected = computed(() => props.store.selectedCell?.coords === props.cell.coords)

// Event handlers
function handleCellClick(event: Event): void {
  const target = event.currentTarget as HTMLElement
  const coords = target.getAttribute('data-coords') || ''
  props.store.setSelectedCell(coords)
  props.store.highlightValue(props.store.selectedCell.value)
}

function handleMouseOver(): void {
  isHovered.value = true
  props.store.hoverCell(props.cell.coords)
}

function handleMouseLeave(): void {
  isHovered.value = false
  props.store.leaveCell(props.cell.coords)
}

// Computed properties
const cellClasses = computed(() => {
  const classes: string[] = []
  
  if (isSelected.value) classes.push('selected')
  if (isHovered.value) classes.push('hovered')
  if (props.cell.protected) classes.push('protected')
  if (props.store.mistakes.has(props.cell.coords)) classes.push('mistake')
  
  return classes
})

// Lifecycle
onMounted(() => {
  // Component initialization
})
</script>

<template>
  <div 
    class="cell"
    :class="cellClasses"
    :data-coords="cell.coords"
    @click="handleCellClick"
    @mouseover="handleMouseOver"
    @mouseleave="handleMouseLeave"
  >
    <div v-if="cell.value > 0" class="cell-value">
      {{ cell.value }}
    </div>
    <div v-else-if="cell.notes.length > 0" class="cell-notes">
      <div v-for="note in cell.notes" :key="note" class="cell-note">
        {{ note }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.cell {
  height: 50px;
  width: 50px;
  font-size: 40px;
  
  &.selected {
    background-color: dimgray;
  }
  
  &.hovered {
    background-color: #c2c2c2;
  }
  
  &.mistake {
    color: darkred;
    background-color: lightcoral;
  }
  
  &.protected {
    font-weight: normal;
  }
  
  &:not(.protected) {
    font-weight: bold;
    font-style: italic;
  }
}
</style>
```

### State Management Pattern (Pinia)

**Store Structure:**
```typescript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Cell } from '@/types/Cell'
import type { CellGroup } from '@/types/CellGroup'

export const useGameStore = defineStore('game', () => {
  // State
  const selectedCell = ref<Cell | null>(null)
  const hoveredCellGroups = ref<Set<CellGroup>>(new Set())
  const mistakes = ref<Set<string>>(new Set())
  const highlightedValue = ref<number | null>(null)

  // Getters
  const hasSelectedCell = computed(() => selectedCell.value !== null)
  const mistakeCount = computed(() => mistakes.value.size)

  // Actions
  function setSelectedCell(coords: string): void {
    const cell = findCellByCoords(coords)
    selectedCell.value = cell
  }

  function hoverCell(coords: string): void {
    const cellGroups = getCellGroupsByCoords(coords)
    hoveredCellGroups.value = new Set(cellGroups)
  }

  function leaveCell(coords: string): void {
    hoveredCellGroups.value.clear()
  }

  function highlightValue(value: number | null): void {
    highlightedValue.value = value
  }

  function addMistake(coords: string): void {
    mistakes.value.add(coords)
  }

  function clearMistakes(): void {
    mistakes.value.clear()
  }

  return {
    // State
    selectedCell,
    hoveredCellGroups,
    mistakes,
    highlightedValue,
    
    // Getters
    hasSelectedCell,
    mistakeCount,
    
    // Actions
    setSelectedCell,
    hoverCell,
    leaveCell,
    highlightValue,
    addMistake,
    clearMistakes
  }
})
```

### API Integration Pattern

**Generated API Client Usage:**
```typescript
import { Api } from '@/generated/Api'
import type { InstanceCreateRequestDto, InstanceCreateResponseDto } from '@/generated/data-contracts'

class GameApiService {
  private api: Api<unknown>

  constructor(baseURL: string) {
    this.api = new Api({
      baseURL,
      timeout: 10000,
    })
  }

  async createGameInstance(request: InstanceCreateRequestDto): Promise<InstanceCreateResponseDto> {
    try {
      const response = await this.api.games.createGameSudokuInstance(request)
      return response.data
    } catch (error) {
      this.handleApiError(error)
      throw error
    }
  }

  async getGameInstance(gameId: string): Promise<InstanceGetResponseDto> {
    try {
      const response = await this.api.games.getGameSudokuInstance(gameId)
      return response.data
    } catch (error) {
      this.handleApiError(error)
      throw error
    }
  }

  private handleApiError(error: unknown): void {
    console.error('API Error:', error)
    // Add centralized error handling logic
  }
}
```

### Real-time Communication Pattern

**Mercure Integration:**
```typescript
class MercureService {
  private eventSource: EventSource | null = null

  constructor(private hubUrl: string) {}

  subscribe(topics: string[], onMessage: (data: any) => void): void {
    const url = new URL(this.hubUrl)
    topics.forEach(topic => url.searchParams.append('topic', topic))

    this.eventSource = new EventSource(url.toString())
    
    this.eventSource.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data)
        onMessage(data)
      } catch (error) {
        console.error('Failed to parse Mercure message:', error)
      }
    }

    this.eventSource.onerror = (error) => {
      console.error('Mercure connection error:', error)
      this.reconnect(topics, onMessage)
    }
  }

  private reconnect(topics: string[], onMessage: (data: any) => void): void {
    setTimeout(() => {
      this.subscribe(topics, onMessage)
    }, 5000) // Reconnect after 5 seconds
  }

  disconnect(): void {
    if (this.eventSource) {
      this.eventSource.close()
      this.eventSource = null
    }
  }
}
```

## Testing Patterns

### Backend Testing Pattern

**Unit Test Structure:**
```php
<?php
namespace Tests\Unit\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\GridGenerator;
use App\Domain\Sudoku\Service\GridShuffler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GridGeneratorTest extends TestCase
{
    private GridShuffler $gridShuffler;
    private GridGenerator $gridGenerator;

    protected function setUp(): void
    {
        $this->gridShuffler = $this->createMock(GridShuffler::class);
        $this->gridGenerator = new GridGenerator($this->gridShuffler);
    }

    #[DataProvider('gridSizeProvider')]
    public function testGenerate(int $size, array $expectedGroups): void
    {
        // Arrange
        $shuffledGrid = ['cells' => []];
        $this->gridShuffler->expects($this->once())
            ->method('shuffle')
            ->willReturn($shuffledGrid);

        // Act
        $result = $this->gridGenerator->generate($size);

        // Assert
        $this->assertEquals($shuffledGrid, $result);
    }

    public static function gridSizeProvider(): array
    {
        return [
            'Small 4x4 grid' => [4, []],
            'Standard 9x9 grid' => [9, []],
            'Large 16x16 grid' => [16, []],
        ];
    }
}
```

### Frontend Testing Pattern

**Component Test Structure:**
```typescript
import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import CellComponent from '@/components/Sudoku/Cell.vue'
import { useGameStore } from '@/stores/GameStore'
import type { Cell } from '@/types/Cell'

describe('Cell Component', () => {
  let store: ReturnType<typeof useGameStore>
  let mockCell: Cell

  beforeEach(() => {
    setActivePinia(createPinia())
    store = useGameStore()
    
    mockCell = {
      coords: '1:1',
      value: 5,
      protected: false,
      notes: []
    }
  })

  it('should render cell value correctly', () => {
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    expect(wrapper.find('.cell-value').text()).toBe('5')
  })

  it('should handle cell click', async () => {
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    await wrapper.find('.cell').trigger('click')
    
    expect(store.selectedCell?.coords).toBe('1:1')
  })

  it('should apply correct CSS classes', () => {
    store.setSelectedCell('1:1')
    
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    expect(wrapper.find('.cell').classes()).toContain('selected')
  })
})
```

## Common Anti-patterns to Avoid

### Backend Anti-patterns
- **Direct Database Access in Controllers**: Always use application services
- **Business Logic in DTOs**: Keep DTOs as simple data containers
- **Circular Dependencies**: Use dependency injection properly
- **Missing Type Declarations**: Always declare parameter and return types
- **Hardcoded Cache Keys**: Use constants or configuration

### Frontend Anti-patterns
- **Direct DOM Manipulation**: Use Vue's reactive system
- **Mixing Business Logic in Components**: Use composables or stores
- **Missing TypeScript Types**: Always define proper interfaces
- **Inline Styles**: Use scoped CSS or CSS modules
- **Unhandled Promise Rejections**: Always handle async errors

These patterns ensure consistency, maintainability, and adherence to the project's architectural principles.