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
        $this->contactHandler = new ContactHandler($this->bot, $this->chatId, $this->db);
        $this->fileHandler = $fileHandler;
    }

    public function handleMessage($text) {
        switch ($text) {
            case '/menu':
                $this->showMainMenu();
                break;
        }
    }

    private function showMainMenu() {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📞 Контакти частини', 'callback_data' => 'contacts']],
                [['text' => '📞 Корисні контакти', 'callback_data' => 'related_contacts']],
                [['text' => '📜 Правила', 'callback_data' => 'rules']],
                [['text' => '📁 Зразки заяв та документів', 'callback_data' => 'files']]
            ]
        ];
        $this->bot->sendMessage($this->chatId, "📌 *Головне меню:*\nОберіть пункт:", [
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'Markdown'
        ]);
    }

    public function handleCallback($callbackData) {
        switch ($callbackData) {
            case 'contacts':
                $this->contactHandler->showContactsMenu();
                break;
            case 'rules':
                $this->bot->sendMessage($this->chatId, $this->rules, ['parse_mode' => 'Markdown']);
                break;
            case 'files':
                $this->fileHandler->showFiles();
                break;
            default:
                if (strpos($callbackData, 'category_') === 0) {
                    $this->contactHandler->handleCallback($callbackData);
                }
                break;
        }
    }

}
