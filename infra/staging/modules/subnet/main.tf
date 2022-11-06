resource "aws_subnet" "sudoku_a" {
  vpc_id     = var.vpc_id
  cidr_block = "10.0.1.0/24"
  map_public_ip_on_launch = true
  availability_zone = "eu-central-1a"

  tags = var.tags
}

resource "aws_subnet" "sudoku_b" {
  vpc_id     = var.vpc_id
  cidr_block = "10.0.2.0/24"
  map_public_ip_on_launch = true
  availability_zone = "eu-central-1b"

  tags = var.tags
}
