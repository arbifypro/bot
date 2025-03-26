<?php

require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/Database.php';
require 'handlers/MenuHandler.php';
require 'handlers/FileHandler.php';
require 'handlers/ContactHandler.php';
require 'handlers/LinksHandler.php';

$bot = new TelegramBot(BOT_TOKEN);
$db = new Database();

$lastUpdateId = 0;

setChatMenuButton();

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
}

function getUpdates($offset) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getUpdates?offset=" . ($offset + 1) . "&timeout=10";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function handleMessage($update, $bot, $db) {
    $chatId = $update['message']['chat']['id'];
    if ($chatId > 0) {
        $bot->sendMessage($chatId, "Користування ботом можливе лише у загальному чаті.");
        return;
    }
    $userId = $update['message']['from']['id'];
    $text = trim($update['message']['text'] ?? '');

    if (empty($text)) {
        return;
    }

    $menuHandler = new MenuHandler($bot, $chatId, $db, $userId);

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

function setChatMenuButton() {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setMyCommands";

    $data = [
        'commands' => [
            ['command' => '/help', 'description' => '📋 Відкрити меню']
        ]
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}
