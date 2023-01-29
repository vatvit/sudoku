terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker"
      version = "2.23.0"
    }
  }

  required_version = ">= 1.2.0"
}

provider "docker" {
  host ="unix:///Users/vatvit/.docker/run/docker.sock"
}

resource "null_resource" "docker_build" {
  triggers = {
    always_run = "${timestamp()}"
  }
  provisioner "local-exec" {
    command = "( cd ./../../ && docker build -t sudoku_nginx -f ./infra/docker/nginx/Dockerfile ./ ) && ( cd ./../../ && docker build -t sudoku_php -f ./infra/docker/php/Dockerfile ./ )"
  }
}

data docker_image "sudoku_nginx" {
  name = "sudoku_nginx"

  depends_on = [null_resource.docker_build]
}

data docker_image "sudoku_php" {
  name = "sudoku_php"

  depends_on = [null_resource.docker_build]
}


resource "docker_container" "nginx" {
  image = data.docker_image.sudoku_nginx.id
  name  = "sudoku_nginx"
  ports {
    internal = 80
    external = 80
  }

  depends_on = [data.docker_image.sudoku_nginx]

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

resource "docker_container" "php" {
  image = data.docker_image.sudoku_php.id
  name  = "sudoku_php"

  depends_on = [data.docker_image.sudoku_php]

  volumes {
    host_path = abspath("${path.module}/../../src")
    container_path = "/var/www/html"
  }

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

resource "docker_network" "sudoku_network" {
  name = "sudoku_network"
}
