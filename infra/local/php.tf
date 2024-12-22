data docker_image "sudoku_php" {
  name = "sudoku_php"

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
    internal = 80
    external = 80
  }

  ports {
    internal = 3000
    external = 3000
  }

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

