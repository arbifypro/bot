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


    public function showContacts() {
        $contacts = $this->db->getContacts();
        $contactList = "";
        foreach ($contacts as $contact) {
            $contactList .= $contact['name'] . "\n";
            $contactList .= $contact['phone'] . "\n";
        }
        $this->bot->sendMessage($this->chatId, "Номери телефонів частини по напрямкам роботи:\n" . $contactList);
    }
}
