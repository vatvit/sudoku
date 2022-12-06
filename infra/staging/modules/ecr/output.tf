output "sudoku_nginx_url" {
  value = aws_ecr_repository.sudoku_nginx.repository_url
}
output "sudoku_php_url" {
  value = aws_ecr_repository.sudoku_php.repository_url
}
