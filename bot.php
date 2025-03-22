<?php
require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/Database.php';
require 'handlers/MenuHandler.php';
require 'handlers/FileHandler.php';
require 'handlers/ContactHandler.php';

$bot = new TelegramBot(BOT_TOKEN);

$db = new Database();

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

                $menuHandler = new MenuHandler($bot, $chatId, $db);
                $menuHandler->handleMessage($text);

                if (isset($update['message']['text'])) {
                    $fileHandler = new FileHandler($bot, $chatId, $db);
                    $fileHandler->downloadFile($update['message']['text']);
                }
            }
        }
    }

    sleep(1);
}
