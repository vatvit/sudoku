variable "cloudwatch_log_group_name" {
  description = "CloudWatch log group name"
  type        = string
}

variable "tags" {
  description = "Tags to set on the CloudWatch group."
  type        = map(string)
  default     = {}
}

variable "retention_in_days" {
  description = "CloudWatch logs retention in days"
  type        = number
  default = 30
}
