resource "aws_db_subnet_group" "sudoku" {
  name       = "sudoku"
  subnet_ids = aws_subnet.public.*.id
}

#data "aws_db_cluster_snapshot" "recent" {
#  db_cluster_identifier = "sudoku-staging"
#  most_recent           = true
#  snapshot_type = "awsbackup"
#}

resource "aws_rds_cluster" "sudoku" {
  cluster_identifier      = "sudoku-staging"
  engine                  = "aurora-mysql"
  engine_mode             = "provisioned"
  engine_version          = "8.0.mysql_aurora.3.04.0"
  availability_zones      = var.availability_zones
  database_name           = "sudoku"
  master_username         = "root"
  master_password         = "changeMe123"
  backup_retention_period = 1
  db_subnet_group_name    = aws_db_subnet_group.sudoku.name
  skip_final_snapshot     = true
#  snapshot_identifier     = data.aws_db_cluster_snapshot.recent.id

  serverlessv2_scaling_configuration {
    max_capacity = 1
    min_capacity = 0.5
  }

  lifecycle {
    ignore_changes = [snapshot_identifier, global_cluster_identifier, availability_zones]
  }
}

resource "aws_rds_cluster_instance" "sudoku" {
  cluster_identifier  = aws_rds_cluster.sudoku.id
  instance_class      = "db.serverless"
  engine              = aws_rds_cluster.sudoku.engine
  engine_version      = aws_rds_cluster.sudoku.engine_version
}