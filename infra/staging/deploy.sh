AWS_ACCOUNT='767345989322'
AWS_REGION='eu-central-1'

AWS_DEPLOY_ECS_CLUSTER='staging'
AWS_DEPLOY_ECS_SERVICE='sudoku'

DESIRED_COUNT=''
if [ -z "$1" ] # stop deploy?
then
  DESIRED_COUNT=" --desired-count=1"
else
  DESIRED_COUNT=" --desired-count=0"
fi
echo "${DESIRED_COUNT}"

aws ecr get-login-password \
    --region ${AWS_REGION} \
| docker login \
    --username AWS \
    --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com

echo "aws ecs update-service --cluster ${AWS_DEPLOY_ECS_CLUSTER} --service ${AWS_DEPLOY_ECS_SERVICE} --force-new-deployment ${DESIRED_COUNT}"
aws ecs update-service \
  --cluster ${AWS_DEPLOY_ECS_CLUSTER} \
  --service ${AWS_DEPLOY_ECS_SERVICE} \
  --force-new-deployment \
  ${DESIRED_COUNT}
