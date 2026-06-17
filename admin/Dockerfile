FROM php:8.2-apache

# نسخ كل ملفات المجلد الرئيسي إلى السيرفر
COPY . /var/www/html/

# إعطاء الصلاحيات الكاملة وتفعيل مود التوجيه
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80