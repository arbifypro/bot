# Вказуємо базовий образ PHP
FROM php:7.4-cli

# Встановлюємо необхідні бібліотеки
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

# Копіюємо ваші файли в контейнер
COPY . /app

# Встановлюємо робочу директорію
WORKDIR /app

# Команда для запуску бота
CMD ["php", "bot.php"]
