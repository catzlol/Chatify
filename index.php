<?php
//before the user connects, a check is made to make sure they're not IP banned.
$userIp = $_SERVER['REMOTE_ADDR'];
$ipBans = file_get_contents("ipbans.txt");
$ipBansArray = explode("\n", $ipBans);

$ipBansArray = array_map('trim', array_filter($ipBansArray));

if (in_array($userIp, $ipBansArray)) {
    echo "You are banned from accessing this site.";
    exit;
}

$userUsername = "";
$usernameData = file_get_contents("username.txt");
$usernameArray = explode("\n", $usernameData);

foreach ($usernameArray as $line) {
    $lineArray = explode("|", $line);

    if (count($lineArray) === 2) {
        list($ip, $username) = $lineArray;

        if ($ip === $userIp) {
            $userUsername = $username;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonymous Text Wall</title>
    <link rel="stylesheet" id="theme-stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($userUsername); ?>!</h1>

    <form method="post" action="set_username.php">
        <label for="username">Set your username:</label>
        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($userUsername); ?>">
        <input type="submit" value="Set Username">
    </form>

    <form id="postForm" method="post" action="post_message.php" enctype="multipart/form-data">
        <label for="message">Write something:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br>
        
        <label for="image">Upload an image:</label>
        <input type="file" name="image" accept="image/*"><br>
        
        <label for="file">Upload a file:</label>
        <input type="file" name="file"><br>

        <label for="video">Upload a video:</label>
        <input type="file" name="video" accept="video/*"><br>
        
        <input type="submit" value="Post">
    </form>




    <h2>Messages:</h2>

    <div id="message-container" class="message-container">
        <?php
        include('get_messages.php');
        ?>
    </div>
</div>

<div class="dark-mode-switch">
    <label for="darkModeSwitch">Dark Mode:</label>
    <input type="checkbox" id="darkModeSwitch" onchange="toggleDarkMode()">
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function toggleDarkMode() {
        const themeStylesheet = document.getElementById('theme-stylesheet');
        const darkModeSwitch = document.getElementById('darkModeSwitch');

        if (darkModeSwitch.checked) {
            themeStylesheet.href = 'styles-dark.css';
            localStorage.setItem('darkMode', 'enabled');
        } else {
            themeStylesheet.href = 'styles.css';
            localStorage.setItem('darkMode', 'disabled');
        }
    }

    function checkInitialDarkModeState() {
        const darkModeSetting = localStorage.getItem('darkMode');
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const themeStylesheet = document.getElementById('theme-stylesheet');

        if (darkModeSetting === 'enabled') {
            darkModeSwitch.checked = true;
            themeStylesheet.href = 'styles-dark.css';
        } else {
            darkModeSwitch.checked = false;
            themeStylesheet.href = 'styles.css';
        }
    }

    checkInitialDarkModeState();
</script>

<script>
    function updateMessages() {
        $('#message-container').load('get_messages.php');
    }

    function displayConnectionLostMessage() {
        $('#message-container').html('<div class="message">Connection lost to the server!</div>');
    }

    $(document).ready(function() {
        updateMessages();

        setInterval(updateMessages, 500);

        $(document).ajaxError(function(event, xhr, settings, error) {
            if (error === 'error') {
                displayConnectionLostMessage();
            }
        });

        $('#postForm').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                timeout: 10000,
                success: function() {
                    updateMessages();
                },
                error: function() {
                    displayConnectionLostMessage();
                }
            });
        });
    });
</script>

</body>
</html>
