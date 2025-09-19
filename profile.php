<?php
session_start();
require 'db_connect.php';

// URLからIDを取得
$public_id = $_GET['id'] ?? '';
if (empty($public_id)) {
    exit('IDが指定されていません。');
}

// データベースからユーザー情報を取得
$stmt = $pdo->prepare("SELECT u.id, u.public_id, u.name, u.bio FROM users u WHERE u.public_id = ?");
$stmt->execute([$public_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーが見つからない場合
if (!$user) {
    exit('ユーザーが見つかりませんでした。');
}

// ユーザーの趣味アイテムを取得
$stmt_user_hobbies = $pdo->prepare("SELECT hi.name FROM user_hobbies uh JOIN hobby_items hi ON uh.hobby_item_id = hi.id WHERE uh.user_id = ?");
$stmt_user_hobbies->execute([$user['id']]);
$user_hobbies_names = $stmt_user_hobbies->fetchAll(PDO::FETCH_COLUMN);

// ログインユーザー（自分）の趣味アイテムIDを取得
$my_hobby_item_ids = $_SESSION['my_hobby_item_ids'] ?? [];

// ログインユーザーの趣味アイテム名を取得
$my_hobbies_names = [];
if (!empty($my_hobby_item_ids)) {
    $placeholders = implode(',', array_fill(0, count($my_hobby_item_ids), '?'));
    $stmt_my_hobbies = $pdo->prepare("SELECT name FROM hobby_items WHERE id IN ($placeholders)");
    $stmt_my_hobbies->execute($my_hobby_item_ids);
    $my_hobbies_names = $stmt_my_hobbies->fetchAll(PDO::FETCH_COLUMN);
}
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
            <?php if (isset($_SESSION['public_id']) && $_SESSION['public_id'] == $user['public_id']): ?>
                <h2>あなたのIDはこちらです</h2>
            <?php else: ?>
                <h2>ユーザーID</h2>
            <?php endif; ?>
            <p class="user-id"><?= htmlspecialchars($user['public_id'], ENT_QUOTES, 'UTF-8') ?></p>
            <hr>
            <h1><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if (!empty($user['bio'])): ?>
            <div class="bio-section">
                <p><?= nl2br(htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <?php endif; ?>
            <h3>趣味</h3>
            <ul>
                <?php foreach ($user_hobbies_names as $hobby): ?>
                    <?php
                        // 共通の趣味かどうかを判定
                        $is_common = in_array($hobby, $my_hobbies_names);
                    ?>
                    <li class="<?= $is_common ? 'common' : '' ?>">
                        <?= htmlspecialchars($hobby, ENT_QUOTES, 'UTF-8') ?>
                        <?= $is_common ? ' (共通!)' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                <a href="chat.php?user_id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>" class="button">チャットする</a>
            <?php endif; ?>
        </div>
        <a href="mypage.php">マイページに戻る</a>
    </div>
</body>
</html>