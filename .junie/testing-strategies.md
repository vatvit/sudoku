# Testing Strategies and Examples

## Backend Testing Approach (PHPUnit)

### Test Structure and Organization

**Directory Structure:**
```
src/backendApp/tests/
├── Unit/                    # Unit tests (namespace: Tests\Unit)
│   ├── Domain/             # Domain layer tests
│   ├── Application/        # Application layer tests
│   └── Infrastructure/     # Infrastructure layer tests
├── Acceptance/             # Integration tests (namespace: Acceptance)
│   ├── Api/               # API endpoint tests
│   └── Database/          # Database integration tests
└── bootstrap.php          # Test bootstrap configuration
```

### Unit Testing Patterns

**Test Class Structure:**
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

    protected function tearDown(): void
    {
        // Clean up resources if needed
    }
}
```

**Arrange-Act-Assert Pattern:**
```php
public function testGenerateValidGrid(): void
{
    // Arrange
    $size = 9;
    $expectedGrid = ['cells' => []];
    $this->gridShuffler->expects($this->once())
        ->method('shuffle')
        ->with($this->anything(), 10)
        ->willReturn($expectedGrid);

    // Act
    $result = $this->gridGenerator->generate($size);

    // Assert
    $this->assertEquals($expectedGrid, $result);
    $this->assertArrayHasKey('cells', $result);
}
```

**Data Provider Usage:**
```php
#[DataProvider('gridSizeProvider')]
public function testGenerateWithDifferentSizes(int $size, bool $shouldSucceed): void
{
    if (!$shouldSucceed) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Grid size must be a perfect square number');
    }

    $result = $this->gridGenerator->generate($size);
    
    if ($shouldSucceed) {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('cells', $result);
    }
}

public static function gridSizeProvider(): array
{
    return [
        'Valid 4x4 grid' => [4, true],
        'Valid 9x9 grid' => [9, true],
        'Valid 16x16 grid' => [16, true],
        'Invalid size 0' => [0, false],
        'Invalid size 10' => [10, false],
        'Invalid negative size' => [-1, false],
    ];
}
```

### Mock Usage Patterns

**Service Mocking:**
```php
public function testCommandHandlerWithMockedServices(): void
{
    // Arrange
    $gameInstanceService = $this->createMock(GameInstanceService::class);
    $cache = $this->createMock(CacheInterface::class);
    $messageBus = $this->createMock(MessageBusInterface::class);
    
    $command = new CreateGameInstanceCommand(size: 9, difficulty: 'medium');
    $expectedDto = new SudokuGameInstanceDto(/* ... */);
    
    $gameInstanceService->expects($this->once())
        ->method('createInstance')
        ->with($command->size, $command->difficulty)
        ->willReturn($expectedDto);

    $handler = new CreateGameInstanceCommandHandler(
        $gameInstanceService,
        $cache,
        $messageBus
    );

    // Act
    $result = $handler($command);

    // Assert
    $this->assertEquals($expectedDto, $result);
}
```

**Partial Mocking:**
```php
public function testServiceWithPartialMocking(): void
{
    $service = $this->getMockBuilder(GameInstanceService::class)
        ->onlyMethods(['validateGameState'])
        ->getMock();
    
    $service->expects($this->once())
        ->method('validateGameState')
        ->willReturn(true);
    
    // Test the actual method while mocking dependencies
    $result = $service->processGameAction($actionDto);
    
    $this->assertTrue($result);
}
```

### Integration Testing (Acceptance Tests)

**API Endpoint Testing:**
```php
<?php
namespace Acceptance\Api\Sudoku;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InstanceControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateGameInstance(): void
    {
        // Arrange
        $requestData = [
            'size' => 9,
            'difficulty' => 'medium'
        ];

        // Act
        $this->client->request(
            'POST',
            '/api/games/sudoku/instances',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('gameId', $responseData);
        $this->assertArrayHasKey('grid', $responseData);
        $this->assertArrayHasKey('status', $responseData);
    }

    public function testCreateGameInstanceWithInvalidData(): void
    {
        // Arrange
        $requestData = [
            'size' => 10, // Invalid size
            'difficulty' => 'invalid'
        ];

        // Act
        $this->client->request(
            'POST',
            '/api/games/sudoku/instances',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $responseData);
    }
}
```

**Database Integration Testing:**
```php
<?php
namespace Acceptance\Database;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class GameInstanceRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private GameInstanceRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(GameInstanceEntity::class);
    }

    public function testSaveAndRetrieveGameInstance(): void
    {
        // Arrange
        $gameInstance = new GameInstanceEntity();
        $gameInstance->setGameId('test-game-id');
        $gameInstance->setStatus(GameStatus::ACTIVE);
        $gameInstance->setGrid([/* grid data */]);

        // Act
        $this->entityManager->persist($gameInstance);
        $this->entityManager->flush();

        $retrievedInstance = $this->repository->findByGameId('test-game-id');

        // Assert
        $this->assertNotNull($retrievedInstance);
        $this->assertEquals('test-game-id', $retrievedInstance->getGameId());
        $this->assertEquals(GameStatus::ACTIVE, $retrievedInstance->getStatus());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
```

### Test Commands and Configuration

**Running Tests:**
```bash
# All tests
composer test

# Unit tests only
composer test-unit

# Acceptance tests only
composer test-acceptance

# Specific test class
composer test-filter GridGeneratorTest

# Test with coverage
vendor/bin/phpunit --coverage-html coverage/

# Test with specific configuration
vendor/bin/phpunit --configuration phpunit.xml.dist
```

**PHPUnit Configuration (phpunit.xml.dist):**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         backupGlobals="false"
         colors="true"
         bootstrap="tests/bootstrap.php">
    <php>
        <ini name="display_errors" value="1" />
        <ini name="error_reporting" value="-1" />
        <server name="APP_ENV" value="test" force="true" />
        <server name="SHELL_VERBOSITY" value="-1" />
    </php>

    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Acceptance">
            <directory>tests/Acceptance</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

## Frontend Testing Approach (Vue.js)

### Test Structure and Organization

**Directory Structure:**
```
src/clientAppVue/
├── src/
│   ├── components/
│   │   └── *.test.ts        # Component unit tests
│   ├── utils/
│   │   └── *.test.ts        # Utility function tests
│   └── stores/
│       └── *.test.ts        # Store tests
├── e2e/                     # End-to-end tests
│   ├── tsconfig.json
│   └── *.spec.ts
└── __snapshots__/           # Jest snapshots (auto-generated)
```

### Unit Testing with Vitest

**Component Testing Pattern:**
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
    expect(wrapper.find('.cell-value').exists()).toBe(true)
  })

  it('should render notes when cell has no value', () => {
    const cellWithNotes = {
      ...mockCell,
      value: 0,
      notes: [1, 2, 3]
    }

    const wrapper = mount(CellComponent, {
      props: {
        cell: cellWithNotes,
        store
      }
    })

    expect(wrapper.find('.cell-notes').exists()).toBe(true)
    expect(wrapper.findAll('.cell-note')).toHaveLength(9) // 3x3 grid for notes
  })

  it('should handle cell click events', async () => {
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    await wrapper.find('.cell').trigger('click')
    
    expect(store.selectedCell?.coords).toBe('1:1')
  })

  it('should apply correct CSS classes based on state', () => {
    store.setSelectedCell('1:1')
    store.addMistake('1:1')
    
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    const cellElement = wrapper.find('.cell')
    expect(cellElement.classes()).toContain('selected')
    expect(cellElement.classes()).toContain('mistake')
  })

  it('should match snapshot', () => {
    const wrapper = mount(CellComponent, {
      props: {
        cell: mockCell,
        store
      }
    })

    expect(wrapper.html()).toMatchSnapshot()
  })
})
```

**Store Testing Pattern:**
```typescript
import { describe, it, expect, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useGameStore } from '@/stores/GameStore'

describe('Game Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should initialize with default state', () => {
    const store = useGameStore()
    
    expect(store.selectedCell).toBeNull()
    expect(store.mistakes.size).toBe(0)
    expect(store.highlightedValue).toBeNull()
  })

  it('should set selected cell correctly', () => {
    const store = useGameStore()
    const mockCell = { coords: '1:1', value: 5, protected: false, notes: [] }
    
    store.setSelectedCell('1:1')
    
    expect(store.selectedCell?.coords).toBe('1:1')
    expect(store.hasSelectedCell).toBe(true)
  })

  it('should manage mistakes correctly', () => {
    const store = useGameStore()
    
    store.addMistake('1:1')
    store.addMistake('2:2')
    
    expect(store.mistakes.size).toBe(2)
    expect(store.mistakes.has('1:1')).toBe(true)
    expect(store.mistakeCount).toBe(2)
    
    store.clearMistakes()
    expect(store.mistakes.size).toBe(0)
  })
})
```

**Utility Function Testing:**
```typescript
import { describe, it, expect } from 'vitest'
import { add, multiply, isEven } from '@/utils/mathUtils'

describe('Math Utils', () => {
  describe('add', () => {
    it('should add two positive numbers', () => {
      expect(add(2, 3)).toBe(5)
    })

    it('should handle zero', () => {
      expect(add(5, 0)).toBe(5)
      expect(add(0, 0)).toBe(0)
    })

    it('should handle negative numbers', () => {
      expect(add(-2, 3)).toBe(1)
      expect(add(-2, -3)).toBe(-5)
    })
  })

  describe('multiply', () => {
    it('should multiply two numbers', () => {
      expect(multiply(3, 4)).toBe(12)
    })

    it('should handle zero', () => {
      expect(multiply(5, 0)).toBe(0)
    })
  })

  describe('isEven', () => {
    it('should return true for even numbers', () => {
      expect(isEven(2)).toBe(true)
      expect(isEven(0)).toBe(true)
      expect(isEven(-4)).toBe(true)
    })

    it('should return false for odd numbers', () => {
      expect(isEven(1)).toBe(false)
      expect(isEven(3)).toBe(false)
      expect(isEven(-3)).toBe(false)
    })
  })
})
```

### End-to-End Testing with Playwright

**E2E Test Structure:**
```typescript
import { test, expect } from '@playwright/test'

test.describe('Sudoku Game', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/')
  })

  test('should load the game interface', async ({ page }) => {
    await expect(page.locator('div.greetings > h1')).toHaveText('You did it!')
    await expect(page.locator('.sudoku-grid')).toBeVisible()
  })

  test('should create a new game', async ({ page }) => {
    await page.click('[data-testid="new-game-button"]')
    await page.selectOption('[data-testid="difficulty-select"]', 'medium')
    await page.click('[data-testid="start-game-button"]')
    
    await expect(page.locator('.sudoku-grid')).toBeVisible()
    await expect(page.locator('.cell')).toHaveCount(81) // 9x9 grid
  })

  test('should allow cell selection and value input', async ({ page }) => {
    await page.click('[data-testid="new-game-button"]')
    await page.click('[data-testid="start-game-button"]')
    
    // Click on an empty cell
    const emptyCell = page.locator('.cell:not(.protected)').first()
    await emptyCell.click()
    
    await expect(emptyCell).toHaveClass(/selected/)
    
    // Input a value
    await page.keyboard.press('5')
    await expect(emptyCell.locator('.cell-value')).toHaveText('5')
  })

  test('should highlight related cells on hover', async ({ page }) => {
    await page.click('[data-testid="new-game-button"]')
    await page.click('[data-testid="start-game-button"]')
    
    const cell = page.locator('[data-coords="1:1"]')
    await cell.hover()
    
    // Check that related cells in the same row, column, and block are highlighted
    await expect(page.locator('.cell.hovered')).toHaveCount.greaterThan(1)
  })

  test('should detect and show mistakes', async ({ page }) => {
    await page.click('[data-testid="new-game-button"]')
    await page.click('[data-testid="start-game-button"]')
    
    // Make an invalid move (this would need specific game state setup)
    const cell1 = page.locator('[data-coords="1:1"]')
    const cell2 = page.locator('[data-coords="1:2"]')
    
    await cell1.click()
    await page.keyboard.press('5')
    
    await cell2.click()
    await page.keyboard.press('5') // Same value in same row
    
    await expect(cell2).toHaveClass(/mistake/)
  })
})
```

**Playwright Configuration:**
```typescript
// playwright.config.ts
import { defineConfig, devices } from '@playwright/test'

export default defineConfig({
  testDir: './e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:5173',
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
  ],
  webServer: {
    command: 'npm run dev',
    url: 'http://localhost:5173',
    reuseExistingServer: !process.env.CI,
  },
})
```

### Test Commands and Scripts

**Frontend Test Commands:**
```bash
# Unit tests with Vitest
npm run test:unit

# E2E tests with Playwright
npm run test:e2e

# Run tests in watch mode
npm run test:unit -- --watch

# Generate coverage report
npm run test:unit -- --coverage

# Update snapshots
npm run test:unit -- --update-snapshots
```

### Mock Service Patterns

**API Service Mocking:**
```typescript
import { vi } from 'vitest'
import type { GameApiService } from '@/services/GameApiService'

const mockApiService: Partial<GameApiService> = {
  createGameInstance: vi.fn().mockResolvedValue({
    gameId: 'test-game-id',
    grid: { cells: [] },
    cellGroups: [],
    status: 'active'
  }),
  
  getGameInstance: vi.fn().mockResolvedValue({
    gameId: 'test-game-id',
    grid: { cells: [] },
    cellGroups: [],
    status: 'active'
  }),
  
  submitAction: vi.fn().mockResolvedValue({
    success: true,
    updatedGrid: { cells: [] }
  })
}

// Usage in tests
it('should create game instance', async () => {
  const result = await mockApiService.createGameInstance!({
    size: 9,
    difficulty: 'medium'
  })
  
  expect(result.gameId).toBe('test-game-id')
  expect(mockApiService.createGameInstance).toHaveBeenCalledWith({
    size: 9,
    difficulty: 'medium'
  })
})
```

### Test Data Management

**Fixture Creation:**
```typescript
// test/fixtures/gameFixtures.ts
export const createMockCell = (overrides: Partial<Cell> = {}): Cell => ({
  coords: '1:1',
  value: 0,
  protected: false,
  notes: [],
  ...overrides
})

export const createMockGrid = (size: number = 9): Cell[][] => {
  const grid: Cell[][] = []
  for (let row = 1; row <= size; row++) {
    const rowCells: Cell[] = []
    for (let col = 1; col <= size; col++) {
      rowCells.push(createMockCell({
        coords: `${row}:${col}`,
        value: Math.floor(Math.random() * size) + 1
      }))
    }
    grid.push(rowCells)
  }
  return grid
}

export const createMockGameInstance = (overrides: Partial<GameInstance> = {}): GameInstance => ({
  gameId: 'test-game-id',
  grid: createMockGrid(),
  cellGroups: [],
  status: 'active',
  createdAt: new Date(),
  ...overrides
})
```

### Performance Testing

**Load Testing with Artillery (Optional):**
```yaml
# artillery-config.yml
config:
  target: 'http://localhost/api'
  phases:
    - duration: 60
      arrivalRate: 10
scenarios:
  - name: "Create and play game"
    flow:
      - post:
          url: "/games/sudoku/instances"
          json:
            size: 9
            difficulty: "medium"
          capture:
            - json: "$.gameId"
              as: "gameId"
      - post:
          url: "/games/sudoku/instances/{{ gameId }}/actions"
          json:
            type: "SET_VALUE"
            coords: "1:1"
            value: 5
```

These testing strategies ensure comprehensive coverage of both backend and frontend functionality, with clear patterns for unit testing, integration testing, and end-to-end testing.