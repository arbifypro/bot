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

    public function handleMessage($text) {
        if ($this->isFileInList($text)) {
            $this->downloadFile($text);
        } else {
            return;
        }
    }

    private function isFileInList($fileName) {
        $files = $this->getFilesList();
        return in_array($fileName, $files);
    }

    private function getFilesList() {
        return array_column($this->db->getDocuments(), 'name');
    }

    private function downloadFile($fileName) {
        $file = $this->db->getFile($fileName)[0];

        if ($file) {
            $filePath = __DIR__ . '/../files/' . $file['url'];
            var_dump($filePath);
            $this->bot->sendDocument($this->chatId, $filePath);
        } else {
            $this->bot->sendMessage($this->chatId, "Файл не знайдено.");
        }
    }

    public function showFiles() {
        $files = $this->db->getDocuments($this->chatId);

        if (empty($files)) {
            $this->bot->sendMessage($this->chatId, "Немає доступних файлів.");
            return;
        }

        $keyboard = ['keyboard' => []];
        foreach ($files as $file) {
            $keyboard['keyboard'][] = [['text' => $file['name']]];
        }
        $keyboard['keyboard'][] = [['text' => '⬅️ Назад']];
        $keyboard['resize_keyboard'] = true;

        $this->userState[$this->user_id] = 'file_selection';
        $this->bot->sendMessage($this->chatId, "Оберіть файл для завантаження або натисніть 'Назад' для повернення в головне меню:", $keyboard);
    }

    public function goBackToMainMenu() {
        $this->userState[$this->user_id] = null;

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









