output "sudoku_target_group_arn" {
  value = aws_lb_target_group.sudoku_load_balancer_target_group.arn
}
