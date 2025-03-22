<?php

class FileHandler {
    private $bot;
    private $chatId;
    private $directory;

    public function __construct($bot, $chatId, $directory = 'files') {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->directory = $directory;
    }

    public function showFiles() {
        $files = array_diff(scandir(__DIR__ . '/../' . $this->directory), array('..', '.'));

        if (empty($files)) {
            $this->bot->sendMessage($this->chatId, "Немає доступних файлів.");
            return;
        }

        $keyboard = ['keyboard' => []];
        foreach ($files as $file) {
            $keyboard['keyboard'][] = [['text' => $file]];
        }
        $keyboard['resize_keyboard'] = true;

        $this->bot->sendMessage($this->chatId, "Оберіть файл для завантаження:", $keyboard);
    }

    public function downloadFile($fileName) {
        $filePath = __DIR__ . '/../' . $this->directory . '/' . $fileName;

        if (!file_exists($filePath)) {
            $this->bot->sendMessage($this->chatId, "Файл не знайдений.");
            return;
        }

        $this->bot->sendDocument($this->chatId, new CURLFile($filePath));
    }
}
