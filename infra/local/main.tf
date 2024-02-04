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
  host ="unix:///Users/vatvit/.orbstack/run/docker.sock"
#  host ="unix:///Users/vatvit/.docker/run/docker.sock"
}

resource "docker_network" "sudoku_network" {
  name = "sudoku_network"
}
