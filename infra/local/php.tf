data docker_image "sudoku_php" {
  name = "sudoku_php"

  depends_on = [null_resource.docker_build]
}

data docker_image "sudoku_php_nginx" {
  name = "sudoku_php_nginx"

  depends_on = [null_resource.docker_build]
}

resource "docker_container" "php" {
  image = data.docker_image.sudoku_php.id
  name  = "sudoku_php"

  depends_on = [data.docker_image.sudoku_php]

  volumes {
    host_path = abspath("${path.module}/../../src")
    container_path = "/app"
  }

  ports {
    internal = 9000
    external = 9000
  }

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

resource "docker_container" "php_nginx" {
  image = data.docker_image.sudoku_php_nginx.id
  name  = "sudoku_php_nginx"

  depends_on = [data.docker_image.sudoku_php_nginx]

  volumes {
    host_path = abspath("${path.module}/../../src")
    container_path = "/app"
  }

  ports {
    internal = 80
    external = 80
  }

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

