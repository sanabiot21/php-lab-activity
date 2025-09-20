FROM php:8.1-apache

# Copy application files to Apache document root
COPY . /var/www/html/

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Enable Apache rewrite module (useful for clean URLs)
RUN a2enmod rewrite

# Configure Apache to allow .htaccess files
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf

RUN a2enconf override

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start Apache in foreground mode
CMD ["apache2-foreground"]