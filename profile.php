<?php
session_start();
require 'db_connect.php';

// URLからIDを取得
$public_id = $_GET['id'] ?? '';
if (empty($public_id)) {
    exit('IDが指定されていません。');
}

// データベースからユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE public_id = ?");
$stmt->execute([$public_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーが見つからない場合
if (!$user) {
    exit('ユーザーが見つかりませんでした。');
}

// データベースから取得した趣味（文字列）を配列に変換
$user_hobbies = explode(',', $user['hobbies']);
// ログインユーザー（自分）の趣味を取得
$my_hobbies = $_SESSION['my_hobbies'] ?? [];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>さんのプロフィール</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <h2>あなたのIDはこちらです</h2>
            <p class="user-id"><?= htmlspecialchars($user['public_id'], ENT_QUOTES, 'UTF-8') ?></p>
            <hr>
            <h1><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <h3>趣味</h3>
            <ul>
                <?php foreach ($user_hobbies as $hobby): ?>
                    <?php
                        // 共通の趣味かどうかを判定
                        $is_common = in_array($hobby, $my_hobbies);
                    ?>
                    <li class="<?= $is_common ? 'common' : '' ?>">
                        <?= htmlspecialchars($hobby, ENT_QUOTES, 'UTF-8') ?>
                        <?= $is_common ? ' (共通!)' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="index.php">トップに戻る</a>
    </div>
</body>
</html>