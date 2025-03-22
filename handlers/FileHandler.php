<?php

class FileHandler {
    private $bot;
    private $chatId;
    private $db;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
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
        $keyboard['resize_keyboard'] = true;

        $this->bot->sendMessage($this->chatId, "Оберіть файл для завантаження:", $keyboard);
    }

    public function downloadFile($fileName) {
        $file = $this->db->getFile($fileName);

        if (!$file) {
            $this->bot->sendMessage($this->chatId, "Файл не знайдений.");
            return;
        }

        $filePath = __DIR__ . '/../files/' . $file['url'];


        if (!file_exists($filePath)) {
            $this->bot->sendMessage($this->chatId, $filePath);
            return;
        }

        $this->bot->sendDocument($this->chatId, new CURLFile($filePath));
    }
}
