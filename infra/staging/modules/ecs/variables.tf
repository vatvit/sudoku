variable "cloudwatch_log_group_name" {
  description = "CloudWatch log group name."
  type        = string
}

variable "sudoku_target_group_arn" {
  description = "Sudoku Target group ARN."
  type        = string
}

variable "public_subnets" {
  description = "An array of public subnets."
  type        = list(object({id: string}))
}

variable "desired_count" {
  description = "Desired count of ECS tasks."
  type        = number
  default = 2
}

variable "ecr_repository_sudoku_nginx_url" {
  description = "Sudoku nginx ECR Repository URL."
  type        = string
}

variable "ecr_repository_sudoku_php_url" {
  description = "Sudoku php ECR Repository URL."
  type        = string
}
