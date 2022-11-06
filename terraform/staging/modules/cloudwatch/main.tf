resource "aws_cloudwatch_log_group" "group" {
  name = var.cloudwatch_log_group_name
  tags = var.tags
  retention_in_days = var.retention_in_days
}
