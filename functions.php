<?php
function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $list = file($file, FILE_IGNORE_NEW_LINES) ?: [];
    if (!in_array($email, $list)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $list = file($file, FILE_IGNORE_NEW_LINES) ?: [];
    $filtered = array_filter($list, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $filtered) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = 'Your Verification Code';
    $headers = "From: no-reply@example.com\r\n" .
               "Content-Type: text/html; charset=UTF-8\r\n";
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    mail($email, $subject, $body, $headers);
}

function fetchGitHubTimeline() {
    return file_get_contents('https://www.github.com/timeline');
}

function formatGitHubData($data) {
    $items = [];
    preg_match_all('/class="timeline-event">(.*?)<\/div>/', $data, $matches);
    foreach ($matches[1] as $raw) {
        preg_match('/<h3>(.*?)<\/h3>.*?href=".*?">(.*?)<\/a>/s', 
                   $raw, $m);
        if ($m) $items[] = ['event' => strip_tags($m[1]), 'user' => $m[2]];
    }
    $html = '<h2>GitHub Timeline Updates</h2><table border="1"><tr><th>Event</th><th>User</th></tr>';
    foreach ($items as $i) {
        $html .= "<tr><td>{$i['event']}</td><td>{$i['user']}</td></tr>";
    }
    $html .= '</table>';
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    $subscribers = file($file, FILE_IGNORE_NEW_LINES) ?: [];
    if (empty($subscribers)) return;
    $raw = fetchGitHubTimeline();
    $html = formatGitHubData($raw);
    foreach ($subscribers as $email) {
        $code = generateVerificationCode();
        $unsubscribeLink = "http://localhost/src/unsubscribe.php?email=" . urlencode($email) . "&code=$code";
        $subject = 'Latest GitHub Updates';
        $body = $html . "<p><a href=\"$unsubscribeLink\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
        $headers = "From: no-reply@example.com\r\nContent-Type: text/html; charset=UTF-8\r\n";
        mail($email, $subject, $body, $headers);
        file_put_contents(__DIR__.'/pending_unsub_codes.txt', "$email:$code\n", FILE_APPEND);
    }
}