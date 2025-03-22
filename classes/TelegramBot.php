<?php

class TelegramBot {
    private $token;
    private $apiUrl;

    public function __construct($token) {
        $this->token = $token;
        $this->apiUrl = "https://api.telegram.org/bot" . $this->token . "/";
    }

    public function sendMessage($chatId, $text, $replyMarkup = null) {
        $data = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'];
        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }
        $this->request('sendMessage', $data);
    }

    private function request($method, $data) {
        $url = $this->apiUrl . $method;
        $options = ['http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data)
        ]];
        file_get_contents($url, false, stream_context_create($options));
    }
}
