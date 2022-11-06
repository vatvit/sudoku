resource "aws_lb" "sudoku_load_balancer" {
  name               = var.load_balancer_name
  internal           = false
  load_balancer_type = "application"
  subnets = [for subnet in var.public_subnets : subnet.id]

  tags = var.tags
}

resource "aws_lb_target_group" "sudoku_load_balancer_target_group" {
  name        = "sudoku"
  target_type = "ip"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = var.vpc_id
}

resource "aws_lb_listener" "sudoku_load_balancer_listener" {
  load_balancer_arn = aws_lb.sudoku_load_balancer.arn
  port              = 80
  protocol          = "HTTP"
#  ssl_policy        = lookup(local.services[count.index], "acm_certificate_arn", "") != "" ? "ELBSecurityPolicy-FS-2018-06" : null
#  certificate_arn   = lookup(local.services[count.index], "acm_certificate_arn", null)
  depends_on        = [aws_lb_target_group.sudoku_load_balancer_target_group]

  default_action {
    target_group_arn = aws_lb_target_group.sudoku_load_balancer_target_group.arn
    type             = "forward"
  }
}
