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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>グループ作成</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>新しいグループを作成</h1>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <p class="message success">グループが作成されました！</p>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <p class="message error">エラーが発生しました: <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="create_group.php" method="post">
            <label for="group_name">グループ名:</label>
            <input type="text" id="group_name" name="group_name" required>

            <label for="description">説明 (オプション):</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <button type="submit">グループを作成</button>
        </form>
        <a href="mypage.php">マイページに戻る</a>
    </div>
</body>
</html>