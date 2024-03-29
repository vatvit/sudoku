variable "ecs_cloudwatch_log_group_name" {
  description = "CloudWatch log group name for Sudoku ECS"
  type        = string
  default = "sudoku"
}

variable "repository_sudoku_php_name" {
  description = "Name of the Sudoku php ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_php"
}

variable "repository_sudoku_mercure_name" {
  description = "Name of the Sudoku Mercure ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_mercure"
}

variable "private_subnets" {
  type = list(string)
  default = ["10.0.1.0/24", "10.0.2.0/24"]
}

variable "public_subnets" {
  type = list(string)
  default = ["10.0.3.0/24", "10.0.4.0/24"]
}

variable "availability_zones" {
  type = list(string)
  default = ["eu-central-1a", "eu-central-1b"]
}
