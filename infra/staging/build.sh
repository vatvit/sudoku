AWS_ACCOUNT='767345989322'
AWS_REGION='eu-central-1'
AWS_REPOSITORY_NGINX="${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/sudoku_nginx"
AWS_REPOSITORY_PHP="${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/sudoku_php"

GIT_HASH=$(git rev-parse --short HEAD)

( cd ./../../ && docker build -t ${AWS_REPOSITORY_NGINX}:${GIT_HASH} --target production -f ./infra/docker/nginx/Dockerfile ./ || exit )
( cd ./../../ && docker build -t ${AWS_REPOSITORY_PHP}:${GIT_HASH} --target production -f ./infra/docker/php/Dockerfile ./ || exit )

docker image tag ${AWS_REPOSITORY_NGINX}:${GIT_HASH} ${AWS_REPOSITORY_NGINX}:latest
docker image tag ${AWS_REPOSITORY_PHP}:${GIT_HASH} ${AWS_REPOSITORY_PHP}:latest

aws ecr get-login-password \
    --region ${AWS_REGION} \
| docker login \
    --username AWS \
    --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com || exit

docker push --all-tags ${AWS_REPOSITORY_NGINX}
docker push --all-tags ${AWS_REPOSITORY_PHP}
