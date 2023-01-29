resource "aws_cloudwatch_log_group" "group" {
  name = var.ecs_cloudwatch_log_group_name
  retention_in_days = 3
}
