<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>ログイン</h1>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <p class="message success">登録が完了しました！あなたのIDとパスワードでログインしてください。</p>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <p class="message error">IDまたはパスワードが間違っています。</p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <label for="public_id">あなたのID:</label>
            <input type="text" id="public_id" name="public_id" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">ログイン</button>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>