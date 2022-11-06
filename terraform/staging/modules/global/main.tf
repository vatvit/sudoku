provider "aws" {
  region  = var.aws_region
  shared_config_files = ["/Users/vatvit/.aws/config"]
  shared_credentials_files = ["/Users/vatvit/.aws/credentials"]
}

resource "aws_vpc" "sudoku" {
  cidr_block = "10.0.0.0/16"
}

resource "aws_internet_gateway" "sudoku" {
  vpc_id = aws_vpc.sudoku.id
}
