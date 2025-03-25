<?php

class ContactHandler
{
    private $bot;
    private $chatId;
    private $db;

    public function __construct($bot, $chatId, $db) {
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->db = $db;
    }

    public function showContactsMenu() {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📞 Контакти частини', 'callback_data' => 'contacts_151']],
                [['text' => '📞 Корисні контакти', 'callback_data' => 'related_contacts']],
                [['text' => '⬅️ Назад', 'callback_data' => 'go_back']]
            ]
        ];

        $this->bot->sendMessage($this->chatId, "📌 *Оберіть тип контактів:*", $keyboard);
    }

    public function handleCallback($callbackData) {
        if ($callbackData === 'contacts_151') {
            $this->showContacts();
        } elseif ($callbackData === 'related_contacts') {
            $this->showRelatedContacts();
        }
    }

    private function showContacts() {
        $contacts = $this->db->getContacts();
        if (empty($contacts)) {
            $this->bot->sendMessage($this->chatId, "❌ Контакти частини відсутні.");
            return;
        }

        $contactList = "📞 *Контакти частини:*\n\n";
        foreach ($contacts as $contact) {
            $contactList .= $contact['name'] . "\n📱 " . $contact['phone'] . "\n\n";
        }

        $this->bot->sendMessage($this->chatId, $contactList, ['parse_mode' => 'Markdown']);
    }

    private function showRelatedContacts() {
        $relatedContacts = $this->db->getRelatedContacts();
        if (empty($relatedContacts)) {
            $this->bot->sendMessage($this->chatId, "❌ Корисні контакти відсутні.");
            return;
        }

        $contactList = "📞 *Корисні контакти:*\n\n";
        foreach ($relatedContacts as $contact) {
            $contactList .= $contact['name'] . "\n📱 " . $contact['phone'] . "\n\n";
        }

        $this->bot->sendMessage($this->chatId, $contactList, ['parse_mode' => 'Markdown']);
    }
}
