variable "repository_sudoku_nginx_name" {
  description = "Name of the ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_nginx"
}

variable "repository_sudoku_php_name" {
  description = "Name of the ECR Repository. Must be unique."
  type        = string
  default     = "sudoku_php"
}

variable "tags" {
  description = "Tags to set on the Repository."
  type        = map(string)
  default     = {}
}
