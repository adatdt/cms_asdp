FROM 192.168.62.220:5000/custom-php71-apache:1.1

COPY . /var/www/html/

RUN chown -R www-data:root /var/www/html && chmod -R 775 /var/www/html

EXPOSE 80
#EXPOSE 443
