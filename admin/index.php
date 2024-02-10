<?php
//You need to login! Change this as needed.
$adminUsername = 'admin';
$adminPassword = 'admin123';

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
    || $_SERVER['PHP_AUTH_USER'] !== $adminUsername || $_SERVER['PHP_AUTH_PW'] !== $adminPassword) {
    header('WWW-Authenticate: Basic realm="Admin Section"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newUsernames = isset($_POST['newUsernames']) ? $_POST['newUsernames'] : '';
    if ($newUsernames !== '') {
        file_put_contents("../username.txt", $newUsernames);
        echo "Usernames have been updated.";
        $currentUsernames = file_get_contents("../username.txt");
    }

    $clearChat = isset($_POST['clearChat']) ? $_POST['clearChat'] : '';
    if ($clearChat === 'yes') {
        file_put_contents("../chat.txt", '');
        echo "Chat.txt has been cleared.";
        $currentChat = file_get_contents("../chat.txt");
    }

    $newContent = isset($_POST['newContent']) ? $_POST['newContent'] : '';
    if ($newContent !== '') {
        file_put_contents("../chat.txt", $newContent);
        echo "Chat.txt has been updated.";
        $currentChat = file_get_contents("../chat.txt");
    }

    $systemMessage = isset($_POST['systemMessage']) ? $_POST['systemMessage'] : '';
    if ($systemMessage !== '') {
        $dateTime = date("Y-m-d H:i:s");
        $systemMessage = "IP|System - $dateTime: $systemMessage";
        file_put_contents("../chat.txt", $systemMessage . PHP_EOL, FILE_APPEND);
        echo "System message has been sent.";
        $currentChat = file_get_contents("../chat.txt");
    }

    $ipAction = isset($_POST['ipAction']) ? $_POST['ipAction'] : '';
    $ipToManage = isset($_POST['ipToManage']) ? $_POST['ipToManage'] : '';
    
    if ($ipAction === 'addIp' && $ipToManage !== '') {
        file_put_contents("../ipbans.txt", "$ipToManage\n", FILE_APPEND);
        echo "IP address $ipToManage has been banned.";
    } elseif ($ipAction === 'removeIp' && $ipToManage !== '') {
        $ipBans = file_get_contents("../ipbans.txt");
        $ipBansArray = explode("\n", $ipBans);
        $ipBansArray = array_diff($ipBansArray, array($ipToManage));
        $updatedIpBans = implode("\n", $ipBansArray);
        file_put_contents("../ipbans.txt", $updatedIpBans);
        echo "IP address $ipToManage has been unbanned.";
    }

    // File management
    $fileAction = isset($_POST['fileAction']) ? $_POST['fileAction'] : '';
    $fileToDelete = isset($_POST['fileToDelete']) ? $_POST['fileToDelete'] : '';

    if ($fileAction === 'deleteFile' && $fileToDelete !== '') {
        $filePaths = [
            "../uploads/files/$fileToDelete",
            "../uploads/images/$fileToDelete",
            "../uploads/videos/$fileToDelete"
        ];

        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
                echo "File $fileToDelete has been deleted.";
            }
        }
        echo "File $fileToDelete does not exist in any of the specified directories.";
    }
}

$currentUsernames = file_get_contents("../username.txt");
$currentChat = file_get_contents("../chat.txt");
$currentIpBans = file_get_contents("../ipbans.txt");
$filesInFiles = scandir("../uploads/files");
$filesInFiles = array_diff($filesInFiles, ['.', '..']);
$filesInImages = scandir("../uploads/images");
$filesInImages = array_diff($filesInImages, ['.', '..']);
$filesInVideos = scandir("../uploads/videos");
$filesInVideos = array_diff($filesInVideos, ['.', '..']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles-admin.css">
</head>
<body>

<h1>Admin Panel</h1>

<form method="post" action="">
    <label for="newUsernames">Edit username.txt:</label><br>
    <textarea id="newUsernames" name="newUsernames" rows="10" cols="50"><?php echo htmlspecialchars($currentUsernames); ?></textarea><br>
    <input type="submit" value="Update Usernames">
</form>

<form method="post" action="">
    <label for="clearChat">Clear chat.txt:</label><br>
    <input type="hidden" name="clearChat" value="yes">
    <input type="submit" value="Clear Chat">
</form>

<form method="post" action="">
    <label for="newContent">Edit chat.txt:</label><br>
    <textarea id="newContent" name="newContent" rows="10" cols="50"><?php echo htmlspecialchars($currentChat); ?></textarea><br>
    <input type="submit" value="Update Chat.txt">
</form>

<form method="post" action="">
    <label for="systemMessage">Send System Message:</label><br>
    <textarea id="systemMessage" name="systemMessage" rows="3" cols="50"></textarea><br>
    <input type="submit" value="Send System Message">
</form>

<form method="post" action="">
    <label for="ipAction">IP Bans:</label><br>
    <select name="ipAction" id="ipAction">
        <option value="addIp">Add IP</option>
        <option value="removeIp">Remove IP</option>
    </select>
    <input type="text" name="ipToManage" placeholder="Enter IP">
    <input type="submit" value="Submit IP Action">
</form>

<!-- File management form -->
<form method="post" action="">
    <label for="fileAction">File Management:</label><br>
    <select name="fileAction" id="fileAction">
        <option value="deleteFile">Delete File</option>
    </select>
    <select name="fileToDelete" id="fileToDelete">
        <?php
        $allFiles = array_merge($filesInFiles, $filesInImages, $filesInVideos);
        foreach ($allFiles as $file) : ?>
            <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Submit File Action">
</form>

<h2>Current IP Bans:</h2>
<pre><?php echo htmlspecialchars($currentIpBans); ?></pre>

<h2>Files in uploads/files:</h2>
<pre><?php print_r($filesInFiles); ?></pre>

<h2>Files in uploads/images:</h2>
<pre><?php print_r($filesInImages); ?></pre>

<h2>Files in uploads/videos:</h2>
<pre><?php print_r($filesInVideos); ?></pre>

</body>
</html>
