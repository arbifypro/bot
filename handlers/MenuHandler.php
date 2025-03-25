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


ℹ️ На рахунок інформації про нашу бригаду з інших ресурсів:
📌 Якщо інфа прояснює ситуацію чи стан на деяких територіях які повязані із зникненням/ полоном чи воєнними діями або находження нашої бригади на таких територіях- можна скидувати таку інфу в групу
📌 До будь якої інформації прошу відносить з розумом і розуміти що можливо інформація подана джерелом не з добрими намірами або не являється правдою ( стосується як укр сторони так і рос)
📌 Відео/ фото які містять дуже специфічну інформацію ( смерть, знущання, катування чи щось подібне) яку не всі можуть витримати, або це може повпливати на психічний стан
ЛЮДИНИ - ‼️ ЗАБОРОНА НА
РОЗПОВСЮДЖЕННЯ В ЦІЙ ГРУПІ ‼️
📌 Відео/фото з нашими військовими з
151 омбр яких виявили в полоні - звісно
можна пересилати в цю групу ( прошу переконатись що це не стара
інформація або можливо вони вже є в нашій групі)

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
            case '/допомога':
                $this->showMainMenu();
                break;
        }
    }

    private function showMainMenu() {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📞 Контакти', 'callback_data' => 'contacts']],
                [['text' => '📜 Правила', 'callback_data' => 'rules']],
                [['text' => '📁 Зразки заяв та документів', 'callback_data' => 'files']],
            ]
        ];

        $this->bot->sendMessage($this->chatId, "📌 *Головне меню:*\nОберіть пункт:", $keyboard);

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
            case 'go_back':
                $this->showMainMenu();
                break;
        }
    }

}
