FROM onixspot/unit-php:8.4

COPY . .
COPY ./api.meritokrat.org/php.ini ${PHP_INI_DIR}/php.ini
COPY ./api.meritokrat.org/ca-budle.pem /docker-entrypoint.d/api.meritokrat.org.pem
COPY ./unit.json /docker-entrypoint.d/api.meritokrat.org.json