terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker"
      version = "2.23.0"
    }
  }

  required_version = ">= 1.2.0"
}

provider "docker" {}

resource "null_resource" "docker_build" {
  triggers = {
    always_run = "${timestamp()}"
  }
  provisioner "local-exec" {
    command = "docker build . -t sudoku_nginx"
  }
}

data docker_image "sudoku_nginx" {
  name = "sudoku_nginx"

  depends_on = [null_resource.docker_build]
}

resource "docker_container" "nginx" {
  image = data.docker_image.sudoku_nginx.id
  name  = "sudoku_image"
  ports {
    internal = 80
    external = 8000
  }

  depends_on = [data.docker_image.sudoku_nginx]
}
