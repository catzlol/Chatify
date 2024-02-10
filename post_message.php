<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = $_POST["message"];
    $sanitizedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $userIp = $_SERVER['REMOTE_ADDR'];
    $username = getUsernameByIP($userIp);
    $dateTime = date("Y-m-d H:i:s");
    $imagePath = handleFileUpload("image", "uploads/images/");
    $filePath = handleFileUpload("file", "uploads/files/");
    $videoPath = handleFileUpload("video", "uploads/videos/");
    $fullMessage = "$userIp|$username - $dateTime: $sanitizedMessage";
    if ($imagePath) {
        $fullMessage .= "<br><img src=\"$imagePath\" alt=\"Uploaded Image\" style=\"max-width: 100%;\">";
    }
    if ($filePath) {
        $fullMessage .= "<br><a href=\"$filePath\" download>Download File</a>";
    }
    if ($videoPath) {
        $fullMessage .= "<br><a href=\"$videoPath\" target='_blank'>Watch Video</a>";
    }
    file_put_contents("chat.txt", $fullMessage . PHP_EOL, FILE_APPEND);
}
header("Location: index.php");
exit;

function getUsernameByIP($ip) {
    $lines = file("username.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($savedIp, $username) = explode("|", $line);
        if ($savedIp === $ip) {
            return $username;
        }
    }
    return "Anonymous";
}

function handleFileUpload($inputName, $targetDirectory) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]["error"] == UPLOAD_ERR_OK) {
        $targetFile = $targetDirectory . basename($_FILES[$inputName]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($inputName === "image" && getimagesize($_FILES[$inputName]["tmp_name"]) === false) {
            echo "Error: File is not an image.";
            $uploadOk = 0;
        }
        if ($_FILES[$inputName]["size"] > 5000000) {
            echo "Error: Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Error: Your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
                return $targetFile;
            } else {
                echo "Error: There was an error uploading your file.";
            }
        }
    }
    return null;
}
?>
