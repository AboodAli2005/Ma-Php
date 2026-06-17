FROM php:8.2-apache
COPY . /var/www/html/

# تثبيت إضافة mysqli للاتصال بقاعدة البيانات
RUN docker-php-ext-install mysqli \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80