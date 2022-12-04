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
}

module "subnet" {
  source = "./modules/subnet"

  vpc_id = module.global.sudoku_vpc_id
}

module "ecr" {
  source = "./modules/ecr"
}

module "ecs_cloudwatch" {
  source = "./modules/cloudwatch"

  cloudwatch_log_group_name = var.ecs_cloudwatch_log_group_name
}

module "ecs_load_balancer" {
  source = "./modules/elb"

  vpc_id = module.global.sudoku_vpc_id
  public_subnets = module.subnet.public_subnets
  load_balancer_name = var.ecs_load_balancer_name
}

module "ecs" {
  source = "./modules/ecs"

  cloudwatch_log_group_name = var.ecs_cloudwatch_log_group_name
  sudoku_target_group_arn = module.ecs_load_balancer.sudoku_target_group_arn
  public_subnets = module.subnet.public_subnets
  ecr_repository_sudoku_nginx_arn = module.ecr.sudoku_nginx_arn
  ecr_repository_sudoku_php_arn = module.ecr.sudoku_php_arn
}

module "codedeploy" {
  source = "./modules/codedeploy"

  sudoku_target_group_name = module.ecs_load_balancer.sudoku_target_group_name
  sudoku_ecs_cluster_name = module.ecs.sudoku_ecs_cluster_name
  sudoku_ecs_service_name = module.ecs.sudoku_ecs_service_name
}
