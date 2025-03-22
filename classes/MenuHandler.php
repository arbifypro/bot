<?php

class MenuHandler {
    private $bot;
    private $chatId;

    public function __construct($bot, $chatId) {
        $this->bot = $bot;
        $this->chatId = $chatId;
    }

    public function handleMessage($text) {
        switch ($text) {
            case '/start':
                $this->showMainMenu();
                break;
            case '📞 Контакти частини':
                $this->bot->sendMessage($this->chatId, "Контакти: +380123456789\nАдреса: вул. Прикладна, 10");
                break;
            case '📜 Правила':
                $this->bot->sendMessage($this->chatId, "1. Виконувати накази\n2. Дотримуватись дисципліни");
                break;
            default:
                $this->bot->sendMessage($this->chatId, "Невідома команда. Використовуйте /start для перегляду меню.");
        }
    }

    private function showMainMenu() {
        $keyboard = [
            'keyboard' => [
                [['text' => '📞 Контакти частини']],
                [['text' => '📜 Правила']]
            ],
            'resize_keyboard' => true
        ];
        $this->bot->sendMessage($this->chatId, "Виберіть пункт меню:", $keyboard);
    }
}
