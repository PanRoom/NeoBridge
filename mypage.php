<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// データベースから現在のユーザー情報を取得
$stmt = $pdo->prepare("SELECT public_id, name, hobbies FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // ユーザーが見つからない場合はログアウトしてエラーメッセージを表示
    session_destroy();
    header('Location: login_form.php?error=user_not_found');
    exit();
}

// データベースから取得した趣味（文字列）を配列に変換
$user_hobbies = explode(',', $user['hobbies']);
// ログインユーザー（自分）の趣味を取得 (ここでは自分自身の趣味なので、$user_hobbiesと同じ)
$my_hobbies = $user_hobbies;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <span>こんにちは、<?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>さん (ID: <?= htmlspecialchars($user['public_id'], ENT_QUOTES, 'UTF-8') ?>)</span>
            <a href="mypage.php">マイページ</a>
            <a href="logout.php">ログアウト</a>
        </div>

        <div class="profile-card">
            <h2>あなたのIDはこちらです</h2>
            <p class="user-id"><?= htmlspecialchars($user['public_id'], ENT_QUOTES, 'UTF-8') ?></p>
            <hr>
            <h1><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <h3>趣味</h3>
            <ul>
                <?php foreach ($user_hobbies as $hobby): ?>
                    <li>
                        <?= htmlspecialchars($hobby, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <hr>

        <h2>あなたのグループ</h2>
        <div class="group-list">
            <?php
            // ユーザーが所属するグループを取得
            $stmt_groups = $pdo->prepare("SELECT g.id, g.name FROM groups g JOIN group_members gm ON g.id = gm.group_id WHERE gm.user_id = ?");
            $stmt_groups->execute([$user_id]);
            $my_groups = $stmt_groups->fetchAll(PDO::FETCH_ASSOC);

            if (count($my_groups) > 0) {
                echo '<ul>';
                foreach ($my_groups as $group) {
                    echo '<li><a href="group_profile.php?group_id=' . htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') . '</a></li>';
                }
                echo '</ul>';
            } else {
                echo '<p>まだグループに所属していません。</p>';
            }
            ?>
            <a href="create_group_form.php" class="button">新しいグループを作成</a>
            <a href="group_search.php" class="button button-secondary">グループを検索</a>
        </div>

        <hr>

        <h2>IDでユーザーを検索</h2>
        <p>相手のIDを入力して、プロフィールと共通の趣味を確認しましょう。</p>
        <form action="profile.php" method="get">
            <label for="id">相手のID:</label>
            <input type="text" id="id" name="id" required>
            <button type="submit">検索</button>
        </form>

        <a href="edit_profile_form.php" class="button">プロフィールを編集</a>
    </div>
</body>
</html>