data docker_image "sudoku_db" {
  name = "sudoku_db"

  depends_on = [null_resource.docker_build]
}

resource "docker_container" "db" {
  image = data.docker_image.sudoku_db.id
  name  = "sudoku_db"

  depends_on = [data.docker_image.sudoku_db]

  volumes {
    host_path = abspath("${path.module}/data")
    container_path = "/var/lib/mysql"
  }

  network_mode = "bridge"
  networks_advanced {
    name = "sudoku_network"
  }
}

