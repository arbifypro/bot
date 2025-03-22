<?php
require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/Database.php';
require 'handlers/MenuHandler.php';
require 'handlers/FileHandler.php';
require 'handlers/ContactHandler.php';

$bot = new TelegramBot(BOT_TOKEN);

try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Підключення до бази даних не вдалося: " . $e->getMessage());
}

$lastUpdateId = 0;

while (true) {
    $response = file_get_contents('https://api.telegram.org/bot' . BOT_TOKEN . '/getUpdates?offset=' . ($lastUpdateId + 1));
    $updates = json_decode($response, true);

    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            if (isset($update['message'])) {
                $chatId = $update['message']['chat']['id'];
                $text = $update['message']['text'];

                $lastUpdateId = $update['update_id'];

                $menuHandler = new MenuHandler($bot, $chatId, YOUR_USER_ID);
                $menuHandler->handleMessage($text);

                if (isset($update['message']['document'])) {
                    $fileHandler = new FileHandler($bot, $db);
                    $fileHandler->handleDocument($update['message']['document'], $chatId);
                }
            }
        }
    }

    sleep(1);
}
