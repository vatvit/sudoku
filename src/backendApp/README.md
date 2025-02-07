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

## Mutation Testing
Mutation testing helps ensure your tests are robust. To run mutation testing with [Infection](https://infection.github.io/), execute the following:
```shell
composer infection
```

Make sure `infection.phar` is available in your project root.

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
