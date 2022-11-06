resource "aws_ecs_cluster" "staging" {
  name = "staging"

  setting {
    name  = "containerInsights"
    value = "disabled"
  }

  configuration {
    execute_command_configuration {
      logging = "OVERRIDE"
      log_configuration {
        cloud_watch_log_group_name = var.cloudwatch_log_group_name
      }
    }
  }
}

resource "aws_ecs_service" "sudoku" {
  name            = "sudoku"
  cluster         = aws_ecs_cluster.staging.id
  task_definition = aws_ecs_task_definition.sudoku.arn
  desired_count   = var.desired_count

  deployment_minimum_healthy_percent = 50
  launch_type = "FARGATE"

  load_balancer {
    container_name = "sudoku_nginx" // TODO?
    container_port = 443
    target_group_arn = var.sudoku_target_group_arn
  }

  network_configuration {
    subnets = [for subnet in var.public_subnets : subnet.id]
    assign_public_ip = true
  }
}

resource "aws_ecs_task_definition" "sudoku" {
  family = "sudoku"
  cpu       = 256
  memory    = 512
  container_definitions = jsonencode([
    {
      name      = "sudoku_nginx"
      image     = "${var.ecr_repository_sudoku_nginx_arn}:latest"
      essential = true
      portMappings = [
        {
          containerPort = 443
          hostPort      = 443
        }
      ]
    },
    {
      name      = "sudoku_php"
      image     = "${var.ecr_repository_sudoku_php_arn}:latest"
      essential = true
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
        }
      ]
    }
  ])
  network_mode = "awsvpc"
  requires_compatibilities = [
    "FARGATE"
  ]
}
