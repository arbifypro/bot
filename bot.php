<?php
require 'config.php';
require 'classes/TelegramBot.php';
require 'classes/MenuHandler.php';

$bot = new TelegramBot(BOT_TOKEN);
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    $menuHandler = new MenuHandler($bot, $chatId);
    $menuHandler->handleMessage($text);
}