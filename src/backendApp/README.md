# Connect to the Container
```shell
docker exec -ti sudoku_php sh
cd /app/backendApp
```

# Composer

```shell
docker exec -ti sudoku_php composer require symfony/orm-pack
```

# Console

```shell
docker exec -ti sudoku_php php bin/console list doctrine
```