# Вказуємо базовий образ PHP
FROM php:7.4-cli

# Встановлюємо необхідні бібліотеки
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

# Встановлюємо бібліотеку для підтримки MySQL/MariaDB
RUN apt-get update && apt-get install -y libmariadb-dev

# Встановлюємо розширення для PDO та MySQL
RUN docker-php-ext-install pdo_mysql

# Копіюємо ваші файли в контейнер
COPY . /app

# Встановлюємо робочу директорію
WORKDIR /app

# Команда для запуску бота
CMD ["php", "index.php"]
