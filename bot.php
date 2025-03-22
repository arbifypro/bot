<?php
require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/MenuHandler.php';

$bot = new TelegramBot(BOT_TOKEN);

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

                // Створення обробника меню
                $menuHandler = new MenuHandler($bot, $chatId);
                $menuHandler->handleMessage($text);
            }
        }
    }

    // Затримка для запобігання надмірного навантаження на сервер
    sleep(1);
}
