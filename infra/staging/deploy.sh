AWS_ACCOUNT='767345989322'
AWS_REGION='eu-central-1'
AWS_REPOSITORY_NGINX='sudoku_nginx'
AWS_REPOSITORY_PHP='sudoku_php'

AWS_DEPLOY_ECS_CLUSTER='staging'
AWS_DEPLOY_ECS_SERVICE='sudoku'

GIT_HASH=$(git rev-parse --short HEAD)

if [ -z "$1" ]
then
  docker build -t ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${AWS_REPOSITORY_NGINX}:${GIT_HASH} --target production ./../docker/nginx
  docker build -t ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${AWS_REPOSITORY_PHP}:${GIT_HASH} --target production ./../../ -f ./../docker/php/Dockerfile
else
  echo "skip build...\n"
fi

DESIRED_COUNT=''
if [ -n $2 ]
then
  DESIRED_COUNT=" --desired-count=$2"
  echo "${DESIRED_COUNT}"
fi

aws ecr get-login-password \
    --region ${AWS_REGION} \
| docker login \
    --username AWS \
    --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com

docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${AWS_REPOSITORY_NGINX}:${GIT_HASH}
docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${AWS_REPOSITORY_PHP}:${GIT_HASH}

echo "aws ecs update-service --cluster ${AWS_DEPLOY_ECS_CLUSTER} --service ${AWS_DEPLOY_ECS_SERVICE} --force-new-deployment ${DESIRED_COUNT}"
aws ecs update-service \
  --cluster ${AWS_DEPLOY_ECS_CLUSTER} \
  --service ${AWS_DEPLOY_ECS_SERVICE} \
  --force-new-deployment \
  ${DESIRED_COUNT}
