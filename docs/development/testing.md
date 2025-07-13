# Testing Guide

## 📋 Purpose of This File

**For AI Assistants**: This file provides comprehensive information about testing in the Sudoku project. Use this when you need to understand the testing architecture, run tests, write new tests, or troubleshoot testing issues. This covers backend PHP tests, frontend unit tests, and end-to-end tests.

**⚠️ AI INSTRUCTION**: DO NOT run `npm run test:unit` without the `--run` flag as it will start in watch mode and block indefinitely waiting for file changes. Always use `npm run test:unit -- --run` for non-blocking execution.

**For Developers**: This document contains all the information needed to understand, run, and write tests for the project. Use it to get familiar with the testing setup, run different types of tests, and follow testing best practices.

**How to Use**:
- Follow the quick start section to run tests immediately
- Reference specific sections for detailed testing instructions
- Use the troubleshooting section for common issues
- Follow the writing tests section when adding new functionality

---

## Current Testing Setup

The Sudoku project uses a comprehensive testing strategy with multiple testing frameworks across different layers:

**Configuration Files**:
- Backend: [`src/backendApp/phpunit.xml.dist`](../src/backendApp/phpunit.xml.dist)
- Frontend: [`src/clientAppVue/playwright.config.ts`](../src/clientAppVue/playwright.config.ts)
- E2E: [`src/clientAppVue/e2e/`](../src/clientAppVue/e2e/) directory

## Quick Commands

```bash
# Local (if PHP/Node installed)
cd src/backendApp && ./bin/phpunit
cd src/clientAppVue && npm run test:unit -- --run    # Non-blocking execution
cd src/clientAppVue && npm run test:e2e

# Docker (Recommended for development)
cd ./infra/local && ./docker_exec_php.sh
# Inside container:
cd /app/backendApp && ./bin/phpunit
cd /app/clientAppVue && npm run test:unit -- --run   # Non-blocking execution
# Note: E2E tests fail in Docker due to web server startup issues

# Docker one-liners (from project root)
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit"
docker exec -ti sudoku_php bash -c "cd /app/clientAppVue && npm run test:unit -- --run"
```

## Backend Tests (PHPUnit)

**Current Tests**: [`src/backendApp/tests/`](../src/backendApp/tests/)
```
tests/
├── Acceptance/Sudoku/          # API integration tests
│   ├── ActionControllerTest.php
│   └── InstanceCreationTest.php
├── Unit/                       # Isolated logic tests
│   ├── Domain/
│   └── Service/
└── bootstrap.php
```

**Key Commands**:
```bash
# Local
./bin/phpunit                                    # All tests
./bin/phpunit tests/Acceptance/                  # API tests only
./bin/phpunit --coverage-html coverage/         # With coverage
./bin/phpunit --filter testMethodName           # Specific test

# Docker (from project root)
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit"
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit tests/Acceptance/"
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit --coverage-html coverage/"
```

**Configuration**: [`phpunit.xml.dist`](../src/backendApp/phpunit.xml.dist) - PHPUnit 11.5.9, `APP_ENV=test`

## Frontend Tests

**Current Tests**: [`src/clientAppVue/`](../src/clientAppVue/)
```
clientAppVue/
├── e2e/                        # Playwright E2E tests
│   ├── vue.spec.ts            # Basic app functionality
│   ├── sudoku-game.spec.ts    # Game mechanics
│   ├── api-integration.spec.ts # API error handling
│   └── helpers/test-helpers.ts # Reusable utilities
└── src/components/            # Components (unit tests TBD)
```

**Unit Tests (Vitest)**:
```bash
# Local
npm run test:unit -- --run          # All unit tests (non-blocking)
npm run test:unit                    # Watch mode (blocks waiting for changes)
npm run test:unit -- --run --coverage # With coverage (non-blocking)

# Docker (from project root)
docker exec -ti sudoku_php bash -c "cd /app/clientAppVue && npm run test:unit -- --run"
```

**⚠️ Important**: By default, `npm run test:unit` runs in watch mode and will block indefinitely waiting for file changes. Always use `-- --run` flag for one-time execution.

**E2E Tests (Playwright)**:
```bash
# Local (recommended for E2E tests)
npm run test:e2e                    # All e2e tests
npx playwright test --headed        # With browser UI
npx playwright test --debug         # Debug mode
npx playwright show-report          # View results

# Docker (FAILS - web server startup issues)
# docker exec -ti sudoku_php bash -c "cd /app/clientAppVue && npm run test:e2e"
# Error: Process from config.webServer exited early
```

**⚠️ E2E Test Issues**: 
- **Docker**: E2E tests fail in Docker due to web server startup problems. Playwright cannot start the dev server properly in the container environment.
- **Recommendation**: Run E2E tests locally outside Docker for reliable execution.
- **Error**: If attempted in Docker, you'll see "Process from config.webServer exited early" and the command will hang.

**Configuration**: [`playwright.config.ts`](../src/clientAppVue/playwright.config.ts) - Chrome/Firefox/Safari, auto dev server

## Reports & Debugging

**Coverage Reports**:
```bash
# Backend HTML coverage (Local)
./bin/phpunit --coverage-html coverage/ && open coverage/index.html

# Backend HTML coverage (Docker)
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit --coverage-html coverage/"

# Frontend E2E reports (Local)
npx playwright show-report

# Frontend E2E reports (Docker)
docker exec -ti sudoku_php bash -c "cd /app/clientAppVue && npx playwright show-report"
```

**Debugging**:
```bash
# Backend verbose/debug (Local)
./bin/phpunit --verbose --debug

# Backend verbose/debug (Docker)
docker exec -ti sudoku_php bash -c "cd /app/backendApp && ./bin/phpunit --verbose --debug"

# E2E debug modes (Local recommended)
npx playwright test --debug          # Step through
npx playwright test --headed         # See browser
npx playwright test --trace on       # Trace failures
```

## Docker Testing Notes

**Container Setup**:
- Use `cd ./infra/local && ./docker_exec_php.sh` to enter container
- All project files are mounted at `/app/` in container
- Container name: `sudoku_php`

**What Works in Docker**:
- ✅ Backend PHPUnit tests (works perfectly)
- ✅ Frontend unit tests with `--run` flag (works well)
- ❌ E2E tests (fail due to web server startup issues)

**Known Issues**:
- npm warnings about deprecated config options (non-critical)
- E2E tests fail with "Process from config.webServer exited early" error
- Xdebug "already loaded" warning (non-critical)
- Unit tests without `--run` flag will block in watch mode

**Recommendations**:
- Use Docker for backend PHPUnit tests (reliable)
- Use Docker for frontend unit tests with `-- --run` flag (reliable)
- Use local environment for E2E tests (Docker E2E tests are not functional)

## Current Coverage & Guidelines

**Coverage Goals**: Backend >80%, Frontend critical paths, E2E main workflows

**Key Patterns**:
- Use helper traits (backend) and test-helpers.ts (frontend)
- Follow AAA pattern (Arrange, Act, Assert)
- Keep tests independent and fast
- Mock external dependencies

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Vitest Documentation](https://vitest.dev/)
- [Playwright Documentation](https://playwright.dev/)
- [Vue Testing Handbook](https://vue-test-utils.vuejs.org/)
- [Symfony Testing Guide](https://symfony.com/doc/current/testing.html)

---
