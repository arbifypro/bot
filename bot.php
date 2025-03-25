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
    $response = getUpdates($lastUpdateId);
    $updates = json_decode($response, true);

    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $lastUpdateId = $update['update_id'];

            if (isset($update['message'])) {
                handleMessage($update, $bot, $db);
            }

            if (isset($update['callback_query'])) {
                handleCallback($update, $bot, $db);
            }
        }
    }

    sleep(1);
}

function getUpdates($offset) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getUpdates?offset=" . ($offset + 1);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function handleMessage($update, $bot, $db) {
    $chatId = $update['message']['chat']['id'];
    $userId = $update['message']['from']['id'];
    $text = trim($update['message']['text'] ?? '');

    if (empty($text)) {
        return;
    }

    $bot->deleteMessage($chatId, $update['message']['']);
    $fileHandler = new FileHandler($bot, $chatId, $db, $userId);
    $menuHandler = new MenuHandler($bot, $chatId, $db, $userId, $fileHandler);

    $menuHandler->handleMessage($text);
}

function handleCallback($update, $bot, $db) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $userId = $callbackQuery['from']['id'];
    $callbackData = $callbackQuery['data'];

    $fileHandler = new FileHandler($bot, $chatId, $db, $userId);
    $contactHandler = new ContactHandler($bot, $chatId, $db);
    $menuHandler = new MenuHandler($bot, $chatId, $db, $userId, $fileHandler);

    $menuHandler->handleCallback($callbackData);
    $fileHandler->handleCallback($callbackData);
    $contactHandler->handleCallback($callbackData);

    $bot->answerCallbackQuery($callbackQuery['id']);
}
