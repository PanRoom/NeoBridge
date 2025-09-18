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
$stmt = $pdo->prepare("SELECT name, hobbies FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // ユーザーが見つからない場合はログアウトしてエラーメッセージを表示
    session_destroy();
    header('Location: login_form.php?error=user_not_found');
    exit();
}

$current_name = $user['name'];
$current_hobbies = explode(',', $user['hobbies']);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>プロフィール編集</h1>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <p class="message success">プロフィールが更新されました！</p>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <p class="message error">エラーが発生しました: <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="update_profile.php" method="post">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($current_name, ENT_QUOTES, 'UTF-8') ?>" required>

            <label>趣味 (複数選択可):</label>
            <div class="hobby-options">
                <?php
                $all_hobbies = ['映画', '音楽', 'スポーツ', '読書']; // 登録フォームと同じ趣味のリスト
                foreach ($all_hobbies as $hobby_option) {
                    $checked = in_array($hobby_option, $current_hobbies) ? 'checked' : '';
                    echo '<label><input type="checkbox" name="hobbies[]" value="' . htmlspecialchars($hobby_option, ENT_QUOTES, 'UTF-8') . '" ' . $checked . '> ' . htmlspecialchars($hobby_option, ENT_QUOTES, 'UTF-8') . '</label>';
                }
                ?>
            </div>

            <label for="password">新しいパスワード (変更しない場合は空欄):</label>
            <input type="password" id="password" name="password">

            <button type="submit">更新</button>
        </form>
        <a href="mypage.php">マイページに戻る</a>
    </div>
</body>
</html>