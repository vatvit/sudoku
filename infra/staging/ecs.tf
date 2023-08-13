resource "aws_ecs_cluster" "staging" {
  name = "staging"

#  setting {
#    name  = "containerInsights"
#    value = "disabled"
#  }
#
#  configuration {
#    execute_command_configuration {
#      logging = "OVERRIDE"
#      log_configuration {
#        cloud_watch_log_group_name = var.ecs_cloudwatch_log_group_name
#      }
#    }
#  }
}

resource "aws_ecs_service" "sudoku" {
  name                               = "sudoku"
  cluster                            = aws_ecs_cluster.staging.id
  task_definition                    = aws_ecs_task_definition.sudoku.arn
  desired_count                      = 1
  deployment_minimum_healthy_percent = 50
  deployment_maximum_percent         = 200
  launch_type                        = "FARGATE"
  scheduling_strategy                = "REPLICA"

  network_configuration {
    subnets          = aws_subnet.public.*.id // TODO: private?
    assign_public_ip = true
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.sudoku_load_balancer_target_group.arn
    container_name   = "sudoku_php"
    container_port   = 80
  }

  lifecycle {
    ignore_changes = [task_definition, desired_count]
  }
}


resource "aws_ecs_task_definition" "sudoku" {
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = 256
  memory                   = 512
  execution_role_arn       = aws_iam_role.ecs_task_execution_role.arn
  task_role_arn            = aws_iam_role.ecs_task_role.arn
  family                   = "sudoku"
  container_definitions = jsonencode([
    {
      name         = "sudoku_php"
      image        = "${aws_ecr_repository.sudoku_php.repository_url}:latest"
      essential    = true
      cpu          = 128
      memory       = 256
      environment  = [
        {"name": "HOST", "value": aws_lb.sudoku_load_balancer.dns_name},
        {"name": "MERCURE_URL", "value": format("%s://%s/.well-known/mercure", "http", aws_lb.sudoku_load_balancer.dns_name)},
        {"name": "MERCURE_PUBLIC_URL", "value": format("%s://%s/.well-known/mercure", "http", aws_lb.sudoku_load_balancer.dns_name)},
        {"name": "DATABASE_URL", "value": format("mysql://%s:%s@%s:3306/%s?serverVersion=8.1", aws_rds_cluster.sudoku.master_username, aws_rds_cluster.sudoku.master_password, aws_rds_cluster.sudoku.endpoint, aws_rds_cluster.sudoku.database_name)}
      ]
      portMappings = [
        {
          protocol      = "tcp"
          containerPort = 80
          hostPort      = 80
        }
      ],
      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-group = var.ecs_cloudwatch_log_group_name
          awslogs-region = "eu-central-1"
          awslogs-stream-prefix = "ecs"
          awslogs-create-group = "true"
        }
      }
    }
  ])
}

resource "aws_iam_role" "ecs_task_role" {
  name = "sudoku-ecsTaskRole"

  assume_role_policy = <<EOF
{
 "Version": "2012-10-17",
 "Statement": [
   {
     "Action": "sts:AssumeRole",
     "Principal": {
       "Service": "ecs-tasks.amazonaws.com"
     },
     "Effect": "Allow",
     "Sid": ""
   }
 ]
}
EOF
}

resource "aws_iam_role" "ecs_task_execution_role" {
  name = "sudoku-ecsTaskExecutionRole"

  assume_role_policy = <<EOF
{
 "Version": "2012-10-17",
 "Statement": [
   {
     "Action": "sts:AssumeRole",
     "Principal": {
       "Service": "ecs-tasks.amazonaws.com"
     },
     "Effect": "Allow",
     "Sid": ""
   }
 ]
}
EOF
}

resource "aws_iam_role_policy_attachment" "ecs-task-execution-role-policy-attachment" {
  role       = aws_iam_role.ecs_task_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_policy" "ECSWriteAccessToCloudWatch" {
  name = "ECSWriteAccessToCloudWatch"
  policy = <<POLICY
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "",
      "Effect": "Allow",
      "Action": [
        "ecr:GetAuthorizationToken",
        "ecr:BatchCheckLayerAvailability",
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "logs:CreateLogStream",
        "logs:PutLogEvents",
        "logs:CreateLogGroup"
      ],
      "Resource": "*"
    }
  ]
}
POLICY
}

resource "aws_iam_role_policy_attachment" "ECSWriteAccessToCloudWatch" {
  policy_arn = aws_iam_policy.ECSWriteAccessToCloudWatch.arn
  role       = aws_iam_role.ecs_task_role.name
}

resource "aws_iam_role_policy_attachment" "ECSWriteAccessToCloudWatchExecutionRole" {
  policy_arn = aws_iam_policy.ECSWriteAccessToCloudWatch.arn
  role       = aws_iam_role.ecs_task_execution_role.name
}

#resource "aws_iam_policy" "ECSReadSecrets" {
#  name = "ECSReadSecrets"
#  policy = <<POLICY
#{
#  "Version": "2012-10-17",
#  "Statement": [
#    {
#      "Sid": "",
#      "Effect": "Allow",
#      "Action": [
#        "ssm:GetParameters",
#        "secretsmanager:*",
#        "kms:Decrypt"
#      ],
#      "Resource": "*"
#    }
#  ]
#}
#POLICY
#}
#
#resource "aws_iam_role_policy_attachment" "ECSReadSecrets" {
#  policy_arn = aws_iam_policy.ECSReadSecrets.arn
#  role       = aws_iam_role.sudoku_deploy.name
#}
#

resource "aws_security_group" "ecs_tasks" {
  name   = "sudoku-sg-task-staging"
  vpc_id = aws_vpc.sudoku.id

  ingress {
    protocol         = "tcp"
    from_port        = 80
    to_port          = 80
    cidr_blocks      = ["0.0.0.0/0"]
    ipv6_cidr_blocks = ["::/0"]
  }

  egress {
    protocol         = "-1"
    from_port        = 0
    to_port          = 0
    cidr_blocks      = ["0.0.0.0/0"]
    ipv6_cidr_blocks = ["::/0"]
  }
}
