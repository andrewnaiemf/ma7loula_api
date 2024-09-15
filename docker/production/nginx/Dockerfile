FROM nginx:1.25.5

WORKDIR /var/www/html

RUN groupadd -g 1000 ma7lola && useradd -u 1000 -g 1000 ma7lola --password password

COPY --chown=ma7lola:ma7lola docker/production/nginx/conf.d /etc/nginx/conf.d

COPY --chown=ma7lola:ma7lola docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

COPY --chown=ma7lola:ma7lola . .

RUN mkdir -p /var/cache/nginx && chown -R ma7lola:ma7lola /var/cache/nginx && \
    mkdir -p /var/log/nginx  && chown -R ma7lola:ma7lola /var/log/nginx && \
    mkdir -p /var/lib/nginx  && chown -R ma7lola:ma7lola /var/lib/nginx && \
    touch /run/nginx.pid && chown -R ma7lola:ma7lola /run/nginx.pid && \
    mkdir -p /etc/nginx/templates /etc/nginx/ssl/certs && \
    chown -R ma7lola:ma7lola /etc/nginx && \
    chmod -R 777 /etc/nginx/conf.d

USER ma7lola

RUN rm -rf /var/www/html/public/storage && ln -s /var/www/html/storage/app/public /var/www/html/public/storage