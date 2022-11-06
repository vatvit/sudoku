output "sudoku_nginx_arn" {
  value = aws_ecr_repository.sudoku_nginx.arn
}
output "sudoku_php_arn" {
  value = aws_ecr_repository.sudoku_php.arn
}
