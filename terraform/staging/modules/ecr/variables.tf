variable "repository_name" {
  description = "Name of the ECR Repository. Must be unique."
  type        = string
}

variable "tags" {
  description = "Tags to set on the Repository."
  type        = map(string)
  default     = {}
}
