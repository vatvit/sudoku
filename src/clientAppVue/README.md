# sudoku

This template should help get you started developing with Vue 3 in Vite.

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```

# Testing

## Unit Tests

Run unit tests with [Vitest](https://vitest.dev/):

```sh
npm run test:unit
```

## End-to-End Tests

Run end-to-end (E2E) tests with [Playwright](https://playwright.dev/):

Install Playwright browsers (if not already installed):
```sh
   npx playwright install
```

Run the E2E tests:
```sh
   npm run test:e2e
```

### Additional Playwright Testing Options:

- **Run tests only on Chromium:**

```sh
  npm run test:e2e -- --project=chromium
```

- **Run tests for a specific file:**

```sh
  npm run test:e2e -- tests/example.spec.ts
```

- **Debug mode:**

```sh
  npm run test:e2e -- --debug
```

## Code Quality

### Linting

Lint and fix code style issues using [ESLint](https://eslint.org/):

```sh
npm run lint
```

### Formatting

Format code with [Prettier](https://prettier.io/):

```sh
npm run format
```

## Type Checking

Run TypeScript type checking for the project using:

```sh
npm run type-check
```

## OpenAPI Client Generation

Generate TypeScript API client from OpenAPI specification:

``` sh
npm run openapi:generate
```

This reads the OpenAPI schema from `./../backendApp/resources/openapi.yaml` and outputs the generated TypeScript API files to `./src/generated`.
