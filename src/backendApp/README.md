# Connect to the Container
```shell
docker exec -ti sudoku_php sh
cd /app/backendApp
```

or

```shell
./infra/local/docker_exec_php.sh
```

# Composer

```shell
docker exec -ti sudoku_php composer require symfony/orm-pack
```

# Console

```shell
docker exec -ti sudoku_php php bin/console list doctrine
```

# Auto tests

# Security

## PHP
**For Local env only**

The script `symfony security:check` runs after `composer install` or `update` command.
