<?php
$botToken = '8293791262:AAGYDvhPjhSN_N057VYvk8SCnS-wO6fyCkQ';
$targetChatId = 7875465751;

$input = file_get_contents('php://input');

function sendTelegramMessage($botToken, $chatId, $text) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $post = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("Telegram send error: " . $err);
        return false;
    }
    return json_decode($resp, true);
}

sendTelegramMessage($botToken, $targetChatId, $input);
exit();
