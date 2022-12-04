resource "aws_codedeploy_app" "sudoku" {
  compute_platform = "ECS"
  name             = "sudoku"
  tags             = var.tags
}

resource "aws_codedeploy_deployment_group" "sudoku" {
  app_name = aws_codedeploy_app.sudoku.name
  deployment_group_name = "sudoku"
  service_role_arn = aws_iam_role.sudoku_deploy.arn

  deployment_style {
    deployment_option = "WITH_TRAFFIC_CONTROL"
    deployment_type   = "BLUE_GREEN"
  }

  load_balancer_info {
    target_group_info {
      name = var.sudoku_target_group_name
    }
  }
  blue_green_deployment_config {
    deployment_ready_option {
      action_on_timeout = "CONTINUE_DEPLOYMENT"
    }

    terminate_blue_instances_on_deployment_success {
      action                           = "TERMINATE"
      termination_wait_time_in_minutes = 5
    }
  }

  ecs_service {
    cluster_name = var.sudoku_ecs_cluster_name
    service_name = var.sudoku_ecs_service_name
  }

  depends_on = [aws_iam_role.sudoku_deploy]
}

resource "aws_iam_role" "sudoku_deploy" {
  name = "SudokuCodeDeployRole"
  assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "",
      "Effect": "Allow",
      "Principal": {
        "Service": "codedeploy.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
EOF
}

resource "aws_iam_role_policy_attachment" "AWSCodeDeployRoleForECS" {
  policy_arn = "arn:aws:iam::aws:policy/AWSCodeDeployRoleForECS"
  role       = aws_iam_role.sudoku_deploy.name
}
