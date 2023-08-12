resource "aws_ecs_service" "sudoku_mercure" {
  name                               = "sudoku_mercure"
  cluster                            = aws_ecs_cluster.staging.id
  task_definition                    = aws_ecs_task_definition.sudoku_mercure.arn
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
    target_group_arn = aws_lb_target_group.sudoku_mercure_load_balancer_target_group.arn
    container_name   = "sudoku_mercure"
    container_port   = 80
  }

  lifecycle {
    ignore_changes = [task_definition, desired_count]
  }
}

resource "aws_ecs_task_definition" "sudoku_mercure" {
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = 256
  memory                   = 512
  execution_role_arn       = aws_iam_role.ecs_task_execution_role_mercure.arn
  task_role_arn            = aws_iam_role.ecs_task_role_mercure.arn
  family                   = "sudoku_mercure"
  container_definitions = jsonencode([
    {
      name         = "sudoku_mercure"
      image        = "${aws_ecr_repository.sudoku_mercure.repository_url}:latest"
      essential    = true
      cpu          = 128
      memory       = 256
      environment  = [
        {"name": "HOST", "value": aws_lb.sudoku_load_balancer.dns_name},
        {"name": "SERVER_NAME", "value": format("%s://%s:80", "http", aws_lb.sudoku_load_balancer.dns_name)}
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

resource "aws_iam_role" "ecs_task_role_mercure" {
  name = "sudoku_mercure-ecsTaskRole"

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

resource "aws_iam_role" "ecs_task_execution_role_mercure" {
  name = "sudoku_mercure-ecsTaskExecutionRole"

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

resource "aws_iam_role_policy_attachment" "ecs-task-execution-role-policy-attachment_mercure" {
  role       = aws_iam_role.ecs_task_execution_role_mercure.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_role_policy_attachment" "ECSWriteAccessToCloudWatch_mercure" {
  policy_arn = aws_iam_policy.ECSWriteAccessToCloudWatch.arn
  role       = aws_iam_role.ecs_task_role_mercure.name
}

resource "aws_iam_role_policy_attachment" "ECSWriteAccessToCloudWatchExecutionRole_mercure" {
  policy_arn = aws_iam_policy.ECSWriteAccessToCloudWatch.arn
  role       = aws_iam_role.ecs_task_execution_role_mercure.name
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

// TODO: what is it ?
resource "aws_security_group" "ecs_tasks_mercure" {
  name   = "sudoku_mercure-sg-task-staging"
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
