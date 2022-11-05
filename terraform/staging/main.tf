terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.16"
    }
  }

  required_version = ">= 1.2.0"
}

module "global" {
  source = "./modules/global"

  aws_region = var.aws_region
}

module "ecr" {
  source = "./modules/ecr"

  repository_name = var.aws_ecr_repository_name
}
