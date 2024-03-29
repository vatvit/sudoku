resource "aws_lb" "sudoku_load_balancer" {
  name               = "sudoku-lb-staging"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.lb.id, data.aws_security_group.sudoku_default.id]
  subnets            = aws_subnet.public.*.id

  enable_deletion_protection = false
}

data "aws_security_group" "sudoku_default" {
  name = "default"

  depends_on = [aws_vpc.sudoku]
}

resource "aws_lb_target_group" "sudoku_load_balancer_target_group" {
  name        = "sudoku-tg-sudoku"
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
    path                = "/status.php"
    unhealthy_threshold = "2"
  }
}

#resource "aws_lb_listener" "http" {
#  load_balancer_arn = aws_lb.sudoku_load_balancer.arn
#  port              = 80
#  protocol          = "HTTP"
#
#  default_action {
#    type = "redirect"
#
#    redirect {
#      port        = 80
#      protocol    = "HTTP"
#      status_code = "HTTP_301"
#    }
#  }
#}

resource "aws_lb_listener" "https" {
  load_balancer_arn = aws_lb.sudoku_load_balancer.arn
  port              = 80
  protocol          = "HTTP"

#  ssl_policy        = "ELBSecurityPolicy-2016-08"
#  certificate_arn   = aws_acm_certificate.lb_tls_cert_arn

  default_action {
    target_group_arn = aws_lb_target_group.sudoku_load_balancer_target_group.arn
    type             = "forward"
  }
}
