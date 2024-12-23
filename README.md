# How to run Local:

## First time:

```shell
cd ./infra/local
terraform init
./apply.sh
./docker_exec_php.sh
```

Run inside the Docker container

```shell
cd /app/backendApp && composer install
./bin/console doctrine:migrations:migrate
cd /app/clientAppVue && npm install
cd /app/clientAppNext && npm install
```

Depends on which a Front framework is needed:
```shell
cd /app/clientAppVue && npm install
npm run dev
```

open http://localhost

# How to run Staging

## From the Scratch

Create file `/Users/vatvit/.aws/credentials` with

```
[default]
aws_access_key_id=**************
aws_secret_access_key=************
```

Install AWS CLI https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html

```shell
cd ./infra/staging
terraform init
terraform apply
./build.sh
./deploy.sh sudoku
./deploy.sh sudoku_mercure
```

# How to apply changes to Local

```shell
terraform apply -auto-approve
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

# How to Shutdown Staging

## Temporary / Partially

```shell
./stop.sh sudoku
./shop.sh sudoku_mercure
```

## Fully

```shell
terraform destroy
```

## XDebug on Local
Enable [Debug Helper](https://chromewebstore.google.com/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) Chrome extension
Enable Debug Listening in PHPStorm

# Troubleshooting

## XDebug

* Container MUST contain XDebug php extension. Use `php -r "phpinfo();" | grep debug` to verify.
* XDebug configuration key in container `client_host` MUST be `host.docker.internal` to communicate with Host machine. Use `XDEBUG_CLIENT_HOST` env var in the Container.
* (additionally can be configured, but should work without it) XDebug configuration key in container `ide_key` MUST be the same as `PHPSTORM`. Use `XDEBUG_IDE_KEY` env var in the Container.
* PHPStorm MUST NOT ignore external connections. Disable checkbox in `Settings -> PHP -> Debug`.

## Docker
* Try to clean-up images