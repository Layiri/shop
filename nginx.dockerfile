FROM nginx:stable-alpine

# Arguments defined in docker-compose.yml
#ARG user
#ARG uid

ADD ./docker/nginx/conf.d/app.conf /etc/nginx/conf.d/app.conf

# Create system user to run Composer and Artisan Commands
#RUN addgroup -S -g $uid $user
#
#RUN adduser -G $user -u $uid -h /home/$user -D $user
#
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html
