FROM php:7.3-apache
RUN docker-php-ext-install mysqli 
RUN docker-php-ext-enable mysqli
RUN apt-get update -y && apt-get install -y sendmail libpng-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev 
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install gd