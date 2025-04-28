# Вибираємо образ PHP
FROM php:7.4-cli

# Встановлюємо додаткові залежності, включаючи MySQL клієнт і бібліотеки для роботи з базою даних
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    libmysqlclient-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Копіюємо файл проекту в контейнер
COPY . /app

# Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Переміщаємося до папки з проектом
WORKDIR /app

# Встановлюємо залежності через Composer
RUN composer install

# Запускаємо бота
CMD ["php", "index.php"]
