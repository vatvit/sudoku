variable "ecs_cloudwatch_log_group_name" {
  description = "CloudWatch log group name for Sudoku ECS"
  type        = string
  default = "sudoku"
}

variable "ecs_load_balancer_name" {
  description = "ELB name for Sudoku ECS"
  type        = string
  default = "sudoku"
}

variable "repository_sudoku_nginx_name" {
  description = "Name of the Sudoku nginx ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_nginx"
}

variable "repository_sudoku_php_name" {
  description = "Name of the Sudoku php ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_php"
}
