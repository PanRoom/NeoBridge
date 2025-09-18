<?php
session_start();
// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー検索</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <span>こんにちは、<?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>さん</span>
            <a href="logout.php">ログアウト</a>
        </div>

        <h2>IDでユーザーを検索</h2>
        <p>相手のIDを入力して、プロフィールと共通の趣味を確認しましょう。</p>
        <form action="profile.php" method="get">
            <label for="id">相手のID:</label>
            <input type="text" id="id" name="id" required>
            <button type="submit">検索</button>
        </form>
    </div>
</body>
</html>