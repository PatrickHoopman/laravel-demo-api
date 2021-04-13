FROM node:13.14-alpine as develop-stage
WORKDIR /app
COPY package*.json ./
RUN npm i -g @quasar/cli
RUN ls
COPY . .
# build stage
FROM develop-stage as build-stage
RUN npm i
RUN quasar build -d
# production stage
FROM nginx:1.17.5-alpine as production-stage
COPY --from=build-stage app/dist/spa /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]