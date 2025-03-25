<?php

class FileHandler {
    private $bot;
    private $chatId;
    private $db;
    private $user_id;
    private $filesPerPage = 5;

    public function __construct($bot, $chatId, $db, $user_id) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
        $this->user_id = $user_id;
    }

    public function showFiles($page = 1) {
        $files = $this->db->getDocuments($this->chatId);

        if (empty($files)) {
            $this->bot->sendMessage($this->chatId, "📂 Немає доступних файлів.");
            return;
        }

        $start = ($page - 1) * $this->filesPerPage;
        $filesToShow = array_slice($files, $start, $this->filesPerPage);


        $keyboard = ['inline_keyboard' => []];
        foreach ($filesToShow as $file) {
            $keyboard['inline_keyboard'][] = [['text' => $file['name'], 'callback_data' => 'file_' . $file['id']]];
        }

        $pagination = [];
        if ($page > 1) {
            $pagination[] = ['text' => '◀ Назад', 'callback_data' => 'page_' . ($page - 1)];
        }
        if (($page * $this->filesPerPage) < count($files)) {
            $pagination[] = ['text' => 'Вперед ▶', 'callback_data' => 'page_' . ($page + 1)];
        }

        $pagination[] = ['text' => 'Головне меню', 'callback_data' => 'go_back'];
        $keyboard['inline_keyboard'][] = $pagination;

        $this->bot->sendMessage($this->chatId, "📌 *Оберіть файл для завантаження:*", $keyboard);
    }

    public function handleCallback($callbackData) {
        if (strpos($callbackData, 'file_') === 0) {
            $fileId = substr($callbackData, 5);
            $this->downloadFile($fileId);
        }

        if (strpos($callbackData, 'page_') === 0) {
            $page = (int)substr($callbackData, 5);
            $this->showFiles($page);
        }
    }

    private function downloadFile($fileId) {
        $file = $this->db->getFile($fileId)[0];

        if ($file) {
            $filePath = __DIR__ . '/../files/' . $file['url'];
            $response = $this->bot->sendDocument($this->chatId, $filePath);
            $responseData = json_decode($response, true);
            if (isset($responseData['result']['message_id'])) {
                $messageId = $responseData['result']['message_id'];
                $this->bot->scheduleDelete($this->chatId, $messageId);
            }
        } else {
            $this->bot->sendMessage($this->chatId, "❌ Файл не знайдено.");
        }
    }
}
