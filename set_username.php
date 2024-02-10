<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $sanitizedUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $userIp = $_SERVER['REMOTE_ADDR'];
    $result = file_put_contents("username.txt", "$userIp|$sanitizedUsername\n", FILE_APPEND);

    if ($result !== false) {
        echo "Username saved successfully!";
    } else {
        echo "Error saving username to file.";
    }

    header("Location: index.php");
    exit;
}
?>
