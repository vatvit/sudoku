variable "vpc_id" {
  description = "VPC Id."
  type        = string
}

variable "public_subnets" {
  description = "An array of public subnets."
  type        = list(object({id: string}))
}

variable "load_balancer_name" {
  description = "Name of Load balancer"
  type        = string
}

variable "tags" {
  description = "Tags to set on the Repository."
  type        = map(string)
  default     = {}
}
