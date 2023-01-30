AWS_ACCOUNT='767345989322'
AWS_REGION='eu-central-1'
AWS_REPOSITORY_PHP="${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/sudoku_php"

#TAG=$(git rev-parse --short HEAD)
TAG=$(date +%s)

( cd ./../../ && docker build -t ${AWS_REPOSITORY_PHP}:${TAG} --target production -f ./infra/docker/php/Dockerfile ./ || exit )

docker image tag ${AWS_REPOSITORY_PHP}:${TAG} ${AWS_REPOSITORY_PHP}:latest

aws ecr get-login-password \
    --region ${AWS_REGION} \
| docker login \
    --username AWS \
    --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com || exit

docker push --all-tags ${AWS_REPOSITORY_PHP}
