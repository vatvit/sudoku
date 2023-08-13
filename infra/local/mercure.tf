data docker_image "sudoku_mercure" {
  name = "sudoku_mercure"

  depends_on = [null_resource.docker_build]
}

resource "docker_container" "mercure" {
  image = data.docker_image.sudoku_mercure.id
  name  = "sudoku_mercure"

  depends_on = [data.docker_image.sudoku_mercure]

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}