FROM node:22-slim AS test

WORKDIR /app

COPY . .
RUN npm install

CMD ["tail", "-f", "/dev/null"]
