<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? null;

if (!$other_user_id) {
    exit('チャット相手のIDが指定されていません。');
}

// 相手のユーザー情報を取得
$stmt = $pdo->prepare("SELECT id, name, public_id FROM users WHERE id = ?");
$stmt->execute([$other_user_id]);
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    exit('ユーザーが見つかりませんでした。');
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($other_user['name'], ENT_QUOTES, 'UTF-8') ?>さんとのチャット</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-window {
            height: 400px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            padding: 1em;
            margin-bottom: 1em;
            background-color: #2a2a2a;
            border-radius: 8px;
        }
        .message-container {
            margin-bottom: 1em;
        }
        .message-container.sent {
            text-align: right;
        }
        .message-container.received {
            text-align: left;
        }
        .message-bubble {
            display: inline-block;
            padding: 0.8em 1.2em;
            border-radius: 18px;
            max-width: 70%;
        }
        .sent .message-bubble {
            background-color: var(--accent-color);
            color: white;
        }
        .received .message-bubble {
            background-color: #444;
        }
        .message-sender {
            font-size: 0.8em;
            color: var(--secondary-text-color);
            margin-bottom: 0.3em;
        }
        .chat-form {
            display: flex;
        }
        .chat-form input {
            flex-grow: 1;
            margin-right: 1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="profile.php?id=<?= htmlspecialchars($other_user['public_id'], ENT_QUOTES, 'UTF-8') ?>">&lt; プロフィールに戻る</a>
            <h2><?= htmlspecialchars($other_user['name'], ENT_QUOTES, 'UTF-8') ?></h2>
        </div>

        <div class="chat-window" id="chat-window"></div>

        <form id="chat-form" class="chat-form">
            <input type="hidden" id="receiver-id" value="<?= htmlspecialchars($other_user_id, ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" id="message-text" placeholder="メッセージを入力..." autocomplete="off" required>
            <button type="submit">送信</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const receiverIdInput = document.getElementById('receiver-id');
        const messageTextInput = document.getElementById('message-text');
        const currentUserId = <?= $current_user_id ?>;

        function scrollToBottom() {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function fetchMessages() {
            fetch(`get_messages.php?user_id=${receiverIdInput.value}`)
                .then(response => response.json())
                .then(messages => {
                    chatWindow.innerHTML = '';
                    messages.forEach(msg => {
                        const messageContainer = document.createElement('div');
                        messageContainer.classList.add('message-container');
                        messageContainer.classList.add(msg.sender_id == currentUserId ? 'sent' : 'received');

                        const senderName = document.createElement('div');
                        senderName.classList.add('message-sender');
                        senderName.textContent = msg.sender_name;

                        const messageBubble = document.createElement('div');
                        messageBubble.classList.add('message-bubble');
                        messageBubble.textContent = msg.message_text;

                        messageContainer.appendChild(senderName);
                        messageContainer.appendChild(messageBubble);
                        chatWindow.appendChild(messageContainer);
                    });
                    scrollToBottom();
                });
        }

        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const receiverId = receiverIdInput.value;
            const messageText = messageTextInput.value;

            if (messageText.trim() === '') return;

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message_text', messageText);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageTextInput.value = '';
                    fetchMessages();
                } else {
                    alert('メッセージの送信に失敗しました。');
                }
            });
        });

        // Fetch messages initially and then every 3 seconds
        fetchMessages();
        setInterval(fetchMessages, 3000);
    });
    </script>
</body>
</html>
