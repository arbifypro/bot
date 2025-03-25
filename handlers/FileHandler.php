<?php

class FileHandler {
    private $bot;
    private $chatId;
    private $db;
    private $user_id;

    public function __construct($bot, $chatId, $db, $user_id) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
        $this->user_id = $user_id;
    }

    public function showFiles() {
        $files = $this->db->getDocuments($this->chatId);

        if (empty($files)) {
            $this->bot->sendMessage($this->chatId, "📂 Немає доступних файлів.");
            return;
        }

        $keyboard = ['inline_keyboard' => []];
        foreach ($files as $file) {
            $keyboard['inline_keyboard'][] = [['text' => $file['name'], 'callback_data' => 'file_' . $file['name']]];
        }
        $keyboard['inline_keyboard'][] = [['text' => '⬅️ Назад', 'callback_data' => 'go_back']];

        $this->bot->sendMessage($this->chatId, "📌 *Оберіть файл для завантаження:*", [
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'Markdown'
        ]);
    }

    public function handleCallback($callbackData) {
        if ($callbackData === 'go_back') {
            $this->goBackToMainMenu();
        } elseif (strpos($callbackData, 'file_') === 0) {
            $fileName = substr($callbackData, 5);
            $this->downloadFile($fileName);
        }
    }

    private function downloadFile($fileName) {
        $file = $this->db->getFile($fileName)[0];

        if ($file) {
            $filePath = __DIR__ . '/../files/' . $file['url'];
            $this->bot->sendDocument($this->chatId, $filePath);
        } else {
            $this->bot->sendMessage($this->chatId, "❌ Файл не знайдено.", [
                'parse_mode' => 'Markdown'
            ]);
        }
    }

    public function goBackToMainMenu() {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📞 Контакти частини', 'callback_data' => 'contacts']],
                [['text' => '📞 Корисні контакти', 'callback_data' => 'related_contacts']],
                [['text' => '📜 Правила', 'callback_data' => 'rules']],
                [['text' => '📁 Зразки заяв та документів', 'callback_data' => 'files']]
            ]
        ];

        $this->bot->sendMessage($this->chatId, "📌 *Головне меню:*", [
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'Markdown'
        ]);
    }
}
