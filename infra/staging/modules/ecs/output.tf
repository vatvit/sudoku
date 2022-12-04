output "sudoku_ecs_cluster_name" {
  value = aws_ecs_cluster.staging.name
}

output "sudoku_ecs_service_name" {
  value = aws_ecs_service.sudoku.name
}
