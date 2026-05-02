# Obtain the base Docker image for the sales leads system Web interface.
FROM php:8.2-apache

# Install dependencies.
RUN apt-get update && \
	
	# Git is needed by Composer.
    apt-get install -y git && \
    
    # Gzip is needed by Composer.
    apt-get install -y unzip && \
    
    # The PDO MySQL extension is not loaded in the image by default.
    # We obviously need this for accessing the database.
    docker-php-ext-install pdo_mysql && \
    
    # The Intl extension is not loaded in the image by default.
    # This is needed to handle localization and internationalization.
    apt-get install -y libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install npm and node.js (for vue.js).
RUN apt-get install -y nodejs npm

# Update the Apache document root directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ir -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ir -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create the PHP configuration file, using the production settings as the base.
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Enable the PDO MySQL extension in the php.ini file.
RUN sed -i -e 's/^;extension=pdo_mysql/extension=pdo_mysql/' /usr/local/etc/php/php.ini
