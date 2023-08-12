# How to run local:

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