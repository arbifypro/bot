<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Bot.php';
require_once __DIR__ . '/Database.php';

$config = require __DIR__ . '/config.php';
$bot = new Bot($config['bot_token']);
$db = new Database($config['db']);

// Бот працюватиме в режимі Long Polling
while (true) {
    // Отримуємо нові оновлення від Telegram
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);

    if (!$update) {
        continue; // Якщо немає оновлення, пропускаємо цикл
    }

    $message = $update['message'] ?? null;

    if ($message) {
        $chatId = $message['chat']['id'];
        $text = trim($message['text']);

        if ($text === '/start') {
            $keyboard = [
                'keyboard' => [
                    [['text' => '📅 Звітність за день'], ['text' => '📆 Звітність за місяць']],
                ],
                'resize_keyboard' => true,
            ];

            $bot->sendMessage($chatId, "👋 Вітаю! Надішліть повідомлення у форматі:\n#payment\nномер_карти\nтип_платежу\nсума_платежу", $keyboard);
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
                continue; // Якщо формат неправильний, пропускаємо обробку
            }

            $card = trim($lines[1]);
            $type = trim($lines[2]);
            $amount = trim($lines[3]);

            if (!is_numeric(str_replace(' ', '', $card)) || !is_numeric($amount)) {
                $bot->sendMessage($chatId, "❗ Номер карти і сума мають бути числовими!");
                continue; // Якщо дані не числові, пропускаємо обробку
            }

            $db->savePayment($card, $type, $amount);
            $bot->sendMessage($chatId, "✅ Платіж збережено!");
        } else {
            $bot->sendMessage($chatId, "❗ Невідома команда. Використовуйте /start.");
        }
    }

    // Затримка між запитами до сервера Telegram (1 секунда)
    sleep(1);
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
