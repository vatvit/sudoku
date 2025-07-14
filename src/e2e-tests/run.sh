#!/bin/sh
set -e

PROJECT=$1
BASE_URL="http://localhost"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

case "$PROJECT" in
  vue)
    TESTS_PATH="clientAppVueTests"
    ;;
  next)
    TESTS_PATH="clientAppNextTests"
    ;;
  *)
    echo "❌ Unknown or missing project: $PROJECT"
    echo "Usage: ./run.sh <vue|next>"
    exit 1
    ;;
esac

echo "🛠 Building base image if not exists..."
docker build -t e2e-tests-base "$SCRIPT_DIR"

echo "📦 Installing dependencies (if not already)..."
docker run --rm \
  -v "$SCRIPT_DIR:/e2e" \
  --network=host \
  e2e-tests-base \
  sh -c "cd /e2e && npm install && npx playwright install --with-deps"

echo "🚀 Running tests from: $TESTS_PATH"
docker run --rm \
  -v "$SCRIPT_DIR:/e2e" \
  --env BASE_URL=$BASE_URL \
  --env TESTS_PATH=$TESTS_PATH \
  --network=host \
  e2e-tests-base
