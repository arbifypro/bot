# Вказуємо базовий образ PHP
FROM php:7.4-cli

# Встановлюємо необхідні бібліотеки
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

RUN apt-get update && apt-get install -y libmysqlclient-dev
RUN docker-php-ext-install pdo_mysql

# Копіюємо ваші файли в контейнер
COPY . /app

# Встановлюємо робочу директорію
WORKDIR /app

# Команда для запуску бота
CMD ["php", "bot.php"]
