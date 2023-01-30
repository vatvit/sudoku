resource "aws_ecr_repository" "sudoku_php" {
  name = var.repository_sudoku_php_name

  force_delete = true
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_lifecycle_policy" "sudoku_php_policy" {
  repository = aws_ecr_repository.sudoku_php.name

  policy = jsonencode({
    rules = [{
      rulePriority = 1
      description  = "keep last 5 images"
      action       = {
        type = "expire"
      }
      selection     = {
        tagStatus   = "any"
        countType   = "imageCountMoreThan"
        countNumber = 5
      }
    }]
  })
}
