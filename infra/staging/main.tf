terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.16"
    }
  }

  required_version = ">= 1.2.0"
}

provider "aws" {
  region = "eu-central-1"
  shared_config_files = ["/Users/vatvit/.aws/config"]
  shared_credentials_files = ["/Users/vatvit/.aws/credentials"]
}

resource "aws_vpc" "sudoku" {
  cidr_block = "10.0.0.0/16"
  tags = {
    Name = "sudoku VPC"
    CreatedAt = formatdate("YYYY-MMM-DD hh:mm:ss", timestamp())
  }
}

resource "aws_internet_gateway" "sudoku" {
  vpc_id = aws_vpc.sudoku.id
}
