FROM nginx:1.27-alpine
COPY ./nginx/conf/conf.d/ /etc/nginx/conf.d/
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]