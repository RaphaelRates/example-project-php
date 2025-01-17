# Usar a imagem PHP com Apache já configurado
FROM php:8.1-apache

# Instalar dependências do sistema e extensões do PHP para MariaDB
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Habilitar o módulo de reescrita do Apache (caso necessário para URLs amigáveis)
RUN a2enmod rewrite

# Ajustar a configuração do Apache para usar a pasta public como DocumentRoot
RUN echo "DocumentRoot /var/www/html/public" > /etc/apache2/sites-available/000-default.conf \
    && echo "<Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    Options Indexes FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf \
    && echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf

# Copiar o código do projeto para o contêiner
COPY . /var/www/html

# Dar permissões corretas para o Apache servir o conteúdo
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependências do Composer
WORKDIR /var/www/html
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Expor a porta 80 do Apache
EXPOSE 80

# Iniciar o Apache em foreground
CMD ["apache2-foreground"]
