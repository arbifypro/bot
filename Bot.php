<?php

class Bot
{
    private $apiUrl;

    public function __construct($token)
    {
        $this->apiUrl = "https://api.telegram.org/bot" . $token . "/";
    }

    public function sendMessage($chatId, $text, $replyMarkup = null, $threadId = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        if ($threadId !== null) {
            $params['message_thread_id'] = $threadId;
        }

        return $this->apiRequest('sendMessage', $params);
    }

    public function apiRequest($method, $params = [])
    {
        $url = $this->apiUrl . $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            error_log('CURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
