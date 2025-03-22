<?php

class FileHandler {
    private $bot;
    private $chatId;
    private $db;
    private $userState;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
        $this->userState = [];
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

        $this->userState[$this->chatId] = 'file_selection';

        $this->bot->sendMessage($this->chatId, "Оберіть файл для завантаження або натисніть 'Назад' для повернення в головне меню:", $keyboard);
    }

    public function downloadFile($fileName) {
        if (!isset($this->userState[$this->chatId]) || $this->userState[$this->chatId] !== 'file_selection') {
            return;
        }

        $file = $this->db->getFile($fileName);

        if (!$file) {
            $this->bot->sendMessage($this->chatId, "Файл не знайдений.");
            return;
        }

        $filePath = __DIR__ . '/../files/' . $file['url'];

        if (!file_exists($filePath)) {
            $this->bot->sendMessage($this->chatId, "Файл не знайдений на сервері.");
            return;
        }

        $this->bot->sendDocument($this->chatId, $filePath);

        $this->userState[$this->chatId] = null;
    }

    public function goBackToMainMenu() {
        $this->userState[$this->chatId] = null;

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


    public function handleMessage($text) {
        if (!isset($this->userState[$this->chatId])) {
            return;
        }

        if ($this->userState[$this->chatId] === 'file_selection') {
            $this->downloadFile($text);
        }
    }
}

