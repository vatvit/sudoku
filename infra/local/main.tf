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
  host ="unix:///Users/vitaliivatulia/.orbstack/run/docker.sock"
#  host ="unix:///Users/vitaliivatulia/.docker/run/docker.sock"
}

resource "docker_network" "sudoku_network" {
  name = "sudoku_network"
}
