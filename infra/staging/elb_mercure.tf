resource "aws_lb_target_group" "sudoku_mercure_load_balancer_target_group" {
  name        = "sudoku-mercure-tg-sudoku"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = aws_vpc.sudoku.id
  target_type = "ip"

  health_check {
    healthy_threshold   = "3"
    interval            = "5"
    protocol            = "HTTP"
    matcher             = "200"
    timeout             = "3"
    path                = "/healthz"
    unhealthy_threshold = "2"
  }
}

resource "aws_lb_listener_rule" "mercure_https" {
  listener_arn = aws_lb_listener.https.arn

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.sudoku_mercure_load_balancer_target_group.arn
  }

  condition {
    path_pattern {
      values = ["/.well-known/mercure/*"]
    }
  }
}
