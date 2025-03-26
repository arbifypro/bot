<?php

require 'config.php';
require 'classes/TelegramBot.php';

$bot = new TelegramBot(BOT_TOKEN);

if ($argc < 3) {
    exit("Usage: php deleteMessage.php <chat_id> <message_id> \n");
}

$chatId = $argv[1];
$messageId = $argv[2];
$time = $argv[3];
if ($time == null) {
    $time = 300;
}
sleep($time); // Чекаємо 20 хвилин
$bot->deleteMessage($chatId, $messageId);
