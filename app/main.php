<?php
$botToken = '8293791262:AAGYDvhPjhSN_N057VYvk8SCnS-wO6fyCkQ';
$targetChatId = 7875465751;

$input = file_get_contents('php://input');
if (!$input) {
  http_response_code(400);
  exit('no input');
}

$update = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  http_response_code(400);
  exit('invalid json');
}

file_put_contents(__DIR__ . '/telegram_updates.log', date('c') . " " . $input . PHP_EOL, FILE_APPEND);

$from = $update['message']['from']['username'] ?? ($update['message']['from']['first_name'] ?? 'unknown');
$text = $update['message']['text'] ?? json_encode($update['message'], JSON_UNESCAPED_SLASHES);

$replyText = "Forwarded from @$from: " . $text;

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

sendTelegramMessage($botToken, $targetChatId, $replyText);

http_response_code(200);
echo 'OK';
