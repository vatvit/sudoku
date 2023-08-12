AWS_ACCOUNT='767345989322'
AWS_REGION='eu-central-1'

AWS_DEPLOY_ECS_CLUSTER='staging'
#AWS_DEPLOY_ECS_SERVICE='sudoku'
#AWS_DEPLOY_ECS_SERVICE_MERCURE='sudoku_mercure'

SERVICE="$1"
echo "${SERVICE}"

aws ecr get-login-password \
    --region ${AWS_REGION} \
| docker login \
    --username AWS \
    --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com

COMMAND="aws ecs update-service --cluster ${AWS_DEPLOY_ECS_CLUSTER} --service ${SERVICE} --force-new-deployment --desired-count 1  --task-definition ${SERVICE}"
echo "${COMMAND}"
${COMMAND}

#aws ecs update-service \
#  --cluster ${AWS_DEPLOY_ECS_CLUSTER} \
#  --service ${SERVICE} \
#  --force-new-deployment \
#  --desired-count=1 \
#  --task-definition=${SERVICE}
