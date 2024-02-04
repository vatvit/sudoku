resource "null_resource" "docker_build" {
  triggers = {
    always_run = "${timestamp()}"
  }
  provisioner "local-exec" {
    command = <<COMMAND
      (
        cd ./../../ &&
        docker build -t sudoku_php -f ./infra/docker/php/local.Dockerfile ./
      ) && (
        cd ./../../ &&
        docker build -t sudoku_mercure -f ./infra/docker/mercure/Dockerfile ./
      ) && (
        cd ./../../ &&
        docker build -t sudoku_db -f ./infra/docker/db/Dockerfile ./
      ) && (
        cd ./../../ &&
        docker build -t sudoku_cache -f ./infra/docker/cache/Dockerfile ./
      )
      COMMAND
  }
}
