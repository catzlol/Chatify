<?php
$messages = array_reverse(file("chat.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

foreach ($messages as $message) {
    $messageParts = explode(' - ', $message, 2);

    if (count($messageParts) === 2) {
        $senderInfo = $messageParts[0];
        $messageContent = $messageParts[1];

        $senderInfoParts = explode('|', $senderInfo, 2);
        $ip = $senderInfoParts[0];
        $username = isset($senderInfoParts[1]) ? $senderInfoParts[1] : "";

        echo "<div class='message'><strong>$username</strong> - $messageContent</div>";
    } else {
        echo "<div class='message'>Error: Invalid message format - $message</div>";
    }
}
?>
