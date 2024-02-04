# How to run Local:

```shell
cd ./infra/local
terraform apply
```
open http://localhost

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

## XDebug on Local
Enable [Debug Helper](https://chromewebstore.google.com/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) Chrome extension
Enable Debug Listening in PHPStorm

### Troubleshooting
* Container MUST contain XDebug php extension. Use `php -r "phpinfo();" | grep debug` to verify.
* XDebug configuration key in container `client_host` MUST be `host.docker.internal` to communicate with Host machine. Use `XDEBUG_CLIENT_HOST` env var in the Container.
* (additionally can be configured, but should work without it) XDebug configuration key in container `ide_key` MUST be the same as `PHPSTORM`. Use `XDEBUG_IDE_KEY` env var in the Container.
* PHPStorm MUST NOT ignore external connections. Disable checkbox in `Settings -> PHP -> Debug`.