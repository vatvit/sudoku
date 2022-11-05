variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "eu-central-1"
}

variable "aws_ecr_repository_name" {
  description = "Name of the AWS ECR Repository. Must be unique."
  type        = string
  default     = "sudoku"
}
