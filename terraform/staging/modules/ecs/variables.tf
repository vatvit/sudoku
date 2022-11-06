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

variable "ecr_repository_sudoku_nginx_arn" {
  description = "Sudoku nginx ECR Repository ARN."
  type        = string
}

variable "ecr_repository_sudoku_php_arn" {
  description = "Sudoku php ECR Repository ARN."
  type        = string
}
