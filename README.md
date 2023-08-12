# How to run Local:

```shell
cd ./infra/local
terraform apply
```
open [http://localhost]

# How to run Staging

```shell
cd ./infra/staging
terraform apply
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

# How to apply changes to Local

```shell
terraform apply
```

# How to apply changes to Staging

**Check no other Deployments in progress**
Otherwise call `./stop.sh sudoku` to stop the whole service and then deploy it again. 

## Terraform changes
```shell
terraform apply
```
## Docker or Codebase changes
```shell
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```