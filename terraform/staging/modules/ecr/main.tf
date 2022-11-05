resource "aws_ecr_repository" "sudoku" {
  name                 = var.repository_name

  image_scanning_configuration {
    scan_on_push = true
  }
}
