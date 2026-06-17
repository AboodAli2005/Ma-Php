FROM php:8.2-apache

# تثبيت إضافة mysqli أولاً لتتعرف عليها صفحات الـ PHP
RUN docker-php-ext-install mysqli

# نسخ كافة الملفات إلى السيرفر
COPY . /var/www/html/

# ضبط الصلاحيات وتفعيل Rewrite المود
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80