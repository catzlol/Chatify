// script.js

function updateMessages() {
    fetch('get_messages.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('message-container').innerHTML = data;
        })
        .catch(error => console.error('Error fetching messages:', error));
}

// Update messages every 5 seconds (adjust the interval as needed)
setInterval(updateMessages, 5000);
