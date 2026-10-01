# 1. Imagen base con PHP 8.3 y Apache
FROM php:8.3-apache

# 2. Instalación de extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# 3. Copiado del proyecto al directorio de Apache
COPY . /var/www/html/

# 4. Configuración del puerto dinámico para Render
ENV PORT 10000
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Si utilizas URLs amigables o .htaccess, descomenta la siguiente línea:
# RUN a2enmod rewrite