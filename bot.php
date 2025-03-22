<?php
require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/MenuHandler.php';
require 'classes/Database.php';  // Підключаємо клас бази даних

$bot = new TelegramBot(BOT_TOKEN);
$db = new Database();  // Створюємо об'єкт бази даних

// Зберігаємо останній оброблений update_id
$lastUpdateId = 0;

while (true) {
    // Отримання оновлень з Telegram API, використовуючи параметр offset
    $response = file_get_contents('https://api.telegram.org/bot' . BOT_TOKEN . '/getUpdates?offset=' . ($lastUpdateId + 1));
    $updates = json_decode($response, true);

    // Якщо є нові повідомлення
    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            if (isset($update['message'])) {
                $chatId = $update['message']['chat']['id'];
                $text = $update['message']['text'];

                // Оновлюємо останній оброблений update_id
                $lastUpdateId = $update['update_id'];

                // Створення обробника меню і передача об'єкта бази даних
                $menuHandler = new MenuHandler($bot, $chatId, $db);  // Тепер передаємо три параметри
                $menuHandler->handleMessage($text);
            }
        }
    }

    // Затримка для запобігання надмірного навантаження на сервер
    sleep(1);
}
