<?php

class MenuHandler {
    private $bot;
    private $chatId;
    private $db;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
    }

    public function handleMessage($text) {
        switch ($text) {
            case '/start':
                $this->showMainMenu();
                break;
            case '📞 Контакти частини':
                $this->showContacts();
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

    private function showContacts() {
        $contacts = $this->db->getContacts();
        $contactList = "";
        foreach ($contacts as $contact) {
            $contactList .= $contact['name'] . "\n";
            $contactList .= $contact['phone_number'] . "\n";
        }
        $this->bot->sendMessage($this->chatId, "Контакти частини:\n\n" . $contactList);
    }
}
