FROM mysql:8.1

# Any `.sql` or `.sh` files in `/docker-entrypoint-initdb.d` are executed during container setup
# doc : https://hub.docker.com/_/mysql

# This Docker image is used for Local env only.
# Non-local envs uses AWS RDS. It is configured in Terraform.

EXPOSE 3306

ENV MYSQL_ROOT_PASSWORD="root"

ENV MYSQL_DATABASE="sudoku"
ENV MYSQL_USER="sudoku"
ENV MYSQL_PASSWORD="sudoku"

COPY ./infra/docker/db/init-db.sql /docker-entrypoint-initdb.d/