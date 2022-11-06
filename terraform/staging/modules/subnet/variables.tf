variable "vpc_id" {
  description = "VPC Id."
  type        = string
}

variable "tags" {
  description = "Tags to set on the Repository."
  type        = map(string)
  default     = {}
}
