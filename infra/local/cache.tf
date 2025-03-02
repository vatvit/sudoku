data docker_image "sudoku_cache" {
  name = "sudoku_cache"

  depends_on = [null_resource.docker_build]
}

resource "docker_container" "cache" {
  image = data.docker_image.sudoku_cache.id
  name  = "sudoku_cache"

  depends_on = [data.docker_image.sudoku_cache]

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }

  ports {
    internal = 6379
    external = 6379
  }
}

