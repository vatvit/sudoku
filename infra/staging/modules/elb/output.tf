output "sudoku_target_group_arn" {
  value = aws_lb_target_group.sudoku_load_balancer_target_group.arn
}

output "sudoku_target_group_name" {
  value = aws_lb_target_group.sudoku_load_balancer_target_group.name
}
