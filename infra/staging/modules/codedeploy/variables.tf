variable "tags" {
  description = "Tags to set on the Deployment App."
  type        = map(string)
  default     = {}
}

variable "sudoku_target_group_name" {
  description = "Sudoku Application Load Balancer Target Group name"
  type        = string
}

variable "sudoku_ecs_cluster_name" {
  description = "Sudoku Application ECS Cluster name"
  type        = string
}

variable "sudoku_ecs_service_name" {
  description = "Sudoku Application ECS Service name"
  type        = string
}
