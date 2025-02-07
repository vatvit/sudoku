# Connect to the Container
```shell
docker exec -ti sudoku_php sh
```

or

```shell
./infra/local/docker_exec_php.sh
```

and then 
```shell
cd /app/backendApp
```

---

# Auto Tests

You can run automated tests using PHPUnit through Composer scripts.

## Run All Tests
```shell
composer test
```

## Run Unit Tests Only
```shell
composer test-unit
```

## Run Acceptance Tests Only
```shell
composer test-acceptance
```

## Run Tests with a Specific Filter
You can filter tests to run specific ones:
```shell
composer test-filter <filter-name>
```

Replace `<filter-name>` with a PHPUnit-compatible filter string. E.g. "ClassTestName".

---

# Running PHP CodeSniffer

To check the coding standards for the project, use PHP CodeSniffer. This will help ensure that the code follows uniform,
clean, and conventional coding practices:

```bash
composer cs-check
```

You can also auto-fix issues (where applicable) using:

```bash
composer cs-fix
```

---

# Running PHPStan

To perform static analysis on your code and ensure proper static types and type safety, use PHPStan. Run the following
command:

```bash
composer phpstan
```

---

## Mutation Testing
Mutation testing helps ensure your tests are robust. To run mutation testing with [Infection](https://infection.github.io/), execute the following:
```shell
composer infection
```

Make sure `infection.phar` is available in your project root.

---

# Running the "check-all" Composer Script

The `check-all` composer script combines all the check commands (`test`, `cs-check`, `phpstan`, and `infection`) into a
single command. This allows developers to run them all in one go and identify issues during the development process. It
stops on the first encountered error, which makes it a useful tool for local development.

You can use the following command:

```bash
composer check-all
```

---

# Security

**For Local env only**

The `symfony security:check` script runs automatically after:
- `composer install`
- `composer update`

It identifies security vulnerabilities in your application dependencies.

If you'd like to run the check manually, execute:
```shell
composer exec symfony security:check
```

---

### Generating OpenAPI Documentation

To generate the OpenAPI documentation for the project, run the following command:

```shell
composer openapi-generate
```

This will use the `nelmio/api-doc-bundle` to export the API documentation in the `yaml` format and save it to `resources/openapi.yaml`.
