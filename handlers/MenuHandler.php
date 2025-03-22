<?php

class MenuHandler {
    private $bot;
    private $chatId;
    private $db;
    private $user_id;

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

    public function __construct($bot, $chatId, $db, $user_id, $fileHandler) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
        $this->user_id = $user_id;
        $this->contactHandler = new ContactHandler($bot, $chatId, $db);
        $this->fileHandler = $fileHandler;
    }

    public function handleMessage($text) {

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
            case '📞 Корисні контакти':
                $this->contactHandler->showRelatedContacts();
                break;
            case '📜 Правила':
                $this->bot->sendMessage($this->chatId, $this->rules);
                break;
            case '📁 Зразки заяв та документів':
                $this->fileHandler->showFiles();
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
