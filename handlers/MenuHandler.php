<?php

class MenuHandler {
    private $bot;
    private $chatId;
    private $db;

    private $contactHandler;
    private $fileHandler;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
        $this->contactHandler = new ContactHandler($bot, $chatId, $db);
        $this->fileHandler = new FileHandler($bot, $chatId, $db);
    }

    public function handleMessage($text) {
//        if ($this->chatId != $this->allowedUserId) {
//            $this->bot->sendMessage($this->chatId, "Тільки адміністратор може відправляти повідомлення.");
//            return;
//        }

        switch ($text) {
            case '/menu':
                $this->showMainMenu();
                break;
            case '📞 Контакти частини':
                $this->contactHandler->showContacts();
                break;
            case '📜 Правила':
                $this->bot->sendMessage($this->chatId, "1. Виконувати накази\n2. Дотримуватись дисципліни");
                break;
            case '📁 Документи':
                $fileHandler = new FileHandler($this->bot, $this->chatId);
                $fileHandler->showFiles();
                break;
            default:
                $this->bot->sendMessage($this->chatId, "Невідома команда. Використовуйте /menu для перегляду меню.");
        }
    }

    private function showMainMenu() {
        $keyboard = [
            'keyboard' => [
                [['text' => '📞 Контакти частини']],
                [['text' => '📜 Правила']],
                [['text' => '📁 Документи']]
            ],
            'resize_keyboard' => true
        ];
        $this->bot->sendMessage($this->chatId, "Виберіть пункт меню:", $keyboard);
    }
}
