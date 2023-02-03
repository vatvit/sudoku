resource "aws_subnet" "public" {
  vpc_id                  = aws_vpc.sudoku.id
  cidr_block              = element(var.public_subnets, count.index)
  availability_zone       = element(var.availability_zones, count.index)
  count                   = length(var.public_subnets)
  map_public_ip_on_launch = true
}

resource "aws_route_table" "public" {
  vpc_id = aws_vpc.sudoku.id
}

resource "aws_route" "public" {
  route_table_id         = aws_route_table.public.id
  destination_cidr_block = "0.0.0.0/0"
  gateway_id             = aws_internet_gateway.sudoku.id
}

resource "aws_route_table_association" "public" {
  count          = length(var.public_subnets)
  subnet_id      = element(aws_subnet.public.*.id, count.index)
  route_table_id = aws_route_table.public.id
}

#resource "aws_subnet" "private" {
#  vpc_id                  = aws_vpc.sudoku.id
#  cidr_block              = element(var.private_subnets, count.index)
#  availability_zone       = element(var.availability_zones, count.index)
#  count                   = length(var.public_subnets)
#  map_public_ip_on_launch = true
#}
#
#resource "aws_nat_gateway" "sudoku" {
#  count         = length(var.private_subnets)
#  allocation_id = element(aws_eip.nat.*.id, count.index)
#  subnet_id     = element(aws_subnet.public.*.id, count.index)
#  depends_on    = [aws_internet_gateway.sudoku]
#}
#
#resource "aws_eip" "nat" {
#  count = length(var.private_subnets)
#  vpc = true
#}
#
#resource "aws_route_table" "private" {
#  count  = length(var.private_subnets)
#  vpc_id = aws_vpc.sudoku.id
#}
#
#resource "aws_route" "private" {
#  count                  = length(compact(var.private_subnets))
#  route_table_id         = element(aws_route_table.private.*.id, count.index)
#  destination_cidr_block = "0.0.0.0/0"
#  nat_gateway_id         = element(aws_nat_gateway.sudoku.*.id, count.index)
#}
#
#resource "aws_route_table_association" "private" {
#  count          = length(var.private_subnets)
#  subnet_id      = element(aws_subnet.private.*.id, count.index)
#  route_table_id = element(aws_route_table.private.*.id, count.index)
#}
