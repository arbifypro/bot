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

    public function sendDocument($chatId, $filePath) {
        $url = "https://api.telegram.org/bot{$this->token}/sendDocument";
        $postFields = [
            'chat_id' => $chatId,
            'document' => new CURLFile($filePath),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
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
