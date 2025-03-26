<?php

class LinksHandler
{
    private $bot;
    private $chatId;
    private $db;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
    }


    public function handleCallback($callbackData) {
        if ($callbackData === 'links') {
            $this->showLinks();
        } elseif ($callbackData === 'admins') {
            $this->showAdmins();
        }
    }

    private function showLinks() {
        $links = $this->db->getLinks();
        if (empty($links)) {
            $this->bot->sendMessage($this->chatId, "❌ Корисні посилання відсутні.");
            return;
        }

        $keyboard = ['inline_keyboard' => []];

        foreach ($links as $link) {
            $keyboard['inline_keyboard'][] = [[
                'text' => $link['name'],
                'url' => $link['url']
            ]];
        }

        $keyboard['inline_keyboard'][] = [['text' => '⬅️ Назад', 'callback_data' => 'go_back']];

        $this->bot->sendMessage($this->chatId, 'Корисні посилання:', $keyboard);
    }

    private function showAdmins() {
        $admins = $this->db->getAdmins();

        $keyboard = ['inline_keyboard' => []];

        foreach ($admins as $admin) {
            $keyboard['inline_keyboard'][] = [[
                'text' => $admin['name'],
                'url' => urlencode($admin['username'])
            ]];
        }
        $keyboard['inline_keyboard'][] = [['text' => '⬅️ Назад', 'callback_data' => 'go_back']];

        $this->bot->sendMessage($this->chatId, 'Адміни:', $keyboard);
    }
}
