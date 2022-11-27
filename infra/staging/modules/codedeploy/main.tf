resource "aws_codedeploy_app" "sudoku" {
  compute_platform = "ECS"
  name             = "sudoku"
  tags             = var.tags
}

data "aws_iam_role" "sudoku_deploy" {
  name = "AWSServiceRoleForECS"
}

resource "aws_codedeploy_deployment_group" "example" {
  app_name = aws_codedeploy_app.sudoku.name
  deployment_group_name = "sudoku"
  service_role_arn = data.aws_iam_role.sudoku_deploy.arn

  depends_on = [data.aws_iam_role.sudoku_deploy]
}
