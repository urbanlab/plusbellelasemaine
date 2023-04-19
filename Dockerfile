# Utilisation de l'image php:5.6-apache comme base
FROM php:5.6-apache

# set timezone
RUN echo "Europe/Paris" > /etc/timezone && dpkg-reconfigure -f noninteractive tzdata

# Installation des dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd mysqli pdo_mysql mcrypt zip

# Installation de Ruby et Compass
RUN apt-get install -y ruby ruby-dev && \
    gem install compass

# Configuration du serveur Apache
#COPY vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copie du code source de l'application
COPY ./html /var/www/html
COPY ./CI_system /var/www/CI_system

# copie des fichiers sample.php
RUN mv /var/www/html/application/config/config.sample.php /var/www/html/application/config/config.php
RUN mv /var/www/html/application/config/database.sample.php /var/www/html/application/config/database.php
RUN mv /var/www/html/application/config/config_specific.sample.php /var/www/html/application/config/config_specific.php



# Donner droit www-data sur le dossier CI_system
RUN chown -R www-data:www-data /var/www/
RUN chmod -R 777 /var/www/


# Configuration de l'application
RUN cd /var/www/html/app && compass compile

WORKDIR /var/www/html

# Suppression du fichier htaccess
#RUN rm /var/www/html/.htaccess

# Exposition du port 80
EXPOSE 80

# Commande à lancer au démarrage du conteneur
CMD ["apache2-foreground"]
