<?php

class MenuHandler {
    private $bot;
    private $chatId;
    private $db;

    private $contactHandler;
    private $fileHandler;

    private $rules = 'Доброго дня, нагадуємо ,
що ця група - добровільно створена  для допомоги у вирішенні питань та обміном досвідом та інформацією.
Адміністратори групи на волонтерських засадах допомагають учасникам.

       📍ПРАВИЛА ГРУПИ📍

☑️Некоректне звернення до будь-кого з учасників групи - 1 попередження , далі видалення.
☑️Образа  будь-кого з учасників чату -видалення 
☑️Розведення  срачу , зради і т.д -1 попередження, далі видалення.
☑️Забороняється постити збори без дозволу адмінів групи.

Просимо відноситись до всіх учасників з повагою. 
Дякуємо за розуміння!💙💛';

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
            case '⬅️ Назад':
                $this->fileHandler->goBackToMainMenu();
                break;
            case '/menu':
                $this->showMainMenu();
                break;
            case '📞 Контакти частини':
                $this->contactHandler->showContacts();
                break;
            case '📜 Правила':
                $this->bot->sendMessage($this->chatId, $this->rules);
                break;
            case '📁 Зразки заяв та документів':
                $fileHandler = new FileHandler($this->bot, $this->chatId, $this->db);
                $fileHandler->showFiles();
                break;
        }
    }

    private function showMainMenu() {
        $keyboard = [
            'keyboard' => [
                [['text' => '📞 Контакти частини']],
                [['text' => '📜 Правила']],
                [['text' => '📁 Зразки заяв та документів']]
            ],
            'resize_keyboard' => true
        ];
        $this->bot->sendMessage($this->chatId, "Виберіть пункт меню:", $keyboard);
    }
}
