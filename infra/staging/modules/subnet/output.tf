output "public_subnets" {
  value = [aws_subnet.sudoku_a, aws_subnet.sudoku_b]
}
