FROM nginx:alpine-slim

# Remove default configuration file
RUN rm /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/

WORKDIR /var/www

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
