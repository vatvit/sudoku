FROM mcr.microsoft.com/playwright:v1.54.1-jammy

WORKDIR /e2e

RUN npm install -g npm@latest

CMD ["sh", "-c", "npx playwright test"]
