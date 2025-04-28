<?php

require 'config.php';
require 'Bot.php';
require 'Database.php';

$bot = new Bot(BOT_TOKEN);
$db = new Database(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD);

$lastUpdateId = 0;

setChatMenuButton();

while (true) {
    $response = getUpdates($lastUpdateId);
    $updates = json_decode($response, true);

    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $lastUpdateId = $update['update_id'];

            if (isset($update['message'])) {
                handleMessage($update, $bot, $db);
            }
        }
    }
}

function getUpdates($offset) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getUpdates?offset=" . ($offset + 1) . "&timeout=10";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function handleMessage($update, $bot, $db) {
    $chatId = $update['message']['chat']['id'];
    $text = trim($update['message']['text'] ?? '');

    if (empty($text)) {
        return;
    }

        if ($text === '/start') {
            $keyboard = [
                'keyboard' => [
                    [['text' => '📅 Звітність за день'], ['text' => '📆 Звітність за місяць']],
                ],
                'resize_keyboard' => true,
            ];

            $bot->sendMessage($chatId, "Вітаю! Вибери тип звітності:", $keyboard);
        } elseif ($text === '📅 Звітність за день') {
            $payments = $db->getPaymentsByDay();
            $report = buildSummaryReport($payments, "Сьогодні");
            $bot->sendMessage($chatId, $report);
        } elseif ($text === '📆 Звітність за місяць') {
            $payments = $db->getPaymentsByMonth();
            $report = buildSummaryReport($payments, "Поточний місяць");
            $bot->sendMessage($chatId, $report);
        } elseif (strpos($text, '#payment') === 0) {
            $lines = explode("\n", $text);

        if (count($lines) !== 4) {
            $bot->sendMessage($chatId, "❗ Невірний формат. Має бути:\n#payment\nномер_карти\nтип_платежу\nсума_платежу");
            return;
        }

        $card = trim($lines[1]);
        $type = trim($lines[2]);
        $amount = trim($lines[3]);

        if (!is_numeric(str_replace(' ', '', $card)) || !is_numeric($amount)) {
            $bot->sendMessage($chatId, "❗ Номер карти і сума мають бути числовими!");
            return;
        }

        $db->savePayment($card, $type, $amount);
        $bot->sendMessage($chatId, "✅ Платіж збережено!");
    }
}

function setChatMenuButton() {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setMyCommands";

    $data = [
        'commands' => [
            ['command' => '/start', 'description' => '🔵 Запустити бота'],
            ['command' => '📅 Звітність за день', 'description' => 'Звіт за день'],
            ['command' => '📆 Звітність за місяць', 'description' => 'Звіт за місяць'],
        ]
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

function buildSummaryReport(array $payments, string $title = ''): string
{
    if (empty($payments)) {
        return "❗ Немає записаних платежів за $title.";
    }

    $typeData = [];
    $totalAmount = 0;
    $totalCount = 0;

    foreach ($payments as $payment) {
        $type = $payment['type'];
        $amount = (float) $payment['amount'];

        if (!isset($typeData[$type])) {
            $typeData[$type] = ['sum' => 0, 'count' => 0];
        }

        $typeData[$type]['sum'] += $amount;
        $typeData[$type]['count'] += 1;

        $totalAmount += $amount;
        $totalCount += 1;
    }

    $lines = [];
    $lines[] = "<b>📊 Звітність: $title</b>";
    $lines[] = "<pre>";
    $lines[] = sprintf("%-20s %-12s %-8s", "Тип платежу", "Сума (грн)", "К-сть");
    $lines[] = str_repeat("-", 44);

    foreach ($typeData as $type => $data) {
        $lines[] = sprintf(
            "%-20s %-12s %-8s",
            mb_substr($type, 0, 20),
            number_format($data['sum'], 2, '.', ' '),
            $data['count']
        );
    }

    $lines[] = str_repeat("-", 44);
    $lines[] = sprintf(
        "%-20s %-12s %-8s",
        "ВСЬОГО",
        number_format($totalAmount, 2, '.', ' '),
        $totalCount
    );
    $lines[] = "</pre>";

    return implode("\n", $lines);
}
