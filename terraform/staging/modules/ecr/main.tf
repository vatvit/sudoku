resource "aws_ecr_repository" "sudoku_nginx" {
  name = var.repository_sudoku_nginx_name

  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_repository" "sudoku_php" {
  name = var.repository_sudoku_php_name

  image_scanning_configuration {
    scan_on_push = true
  }
}


resource "aws_ecr_lifecycle_policy" "sudoku_nginx_policy" {
  repository = aws_ecr_repository.sudoku_nginx.name

  policy = <<EOF
{
    "rules": [
        {
            "rulePriority": 1,
            "description": "Keep last 5 images",
            "selection": {
                "tagStatus": "tagged",
                "tagPrefixList": ["v"],
                "countType": "imageCountMoreThan",
                "countNumber": 50
            },
            "action": {
                "type": "expire"
            }
        }
    ]
}
EOF
}

resource "aws_ecr_lifecycle_policy" "sudoku_php_policy" {
  repository = aws_ecr_repository.sudoku_php.name

  policy = <<EOF
{
    "rules": [
        {
            "rulePriority": 1,
            "description": "Keep last 5 images",
            "selection": {
                "tagStatus": "tagged",
                "tagPrefixList": ["v"],
                "countType": "imageCountMoreThan",
                "countNumber": 50
            },
            "action": {
                "type": "expire"
            }
        }
    ]
}
EOF
}
