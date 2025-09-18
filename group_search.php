<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$search_query = $_GET['query'] ?? '';
$search_results = [];

if (!empty($search_query)) {
    try {
        // グループ名またはIDで検索
        $stmt = $pdo->prepare("SELECT id, name, description FROM groups WHERE name LIKE ? OR id = ?");
        $stmt->execute(['%' . $search_query . '%', $search_query]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = 'グループ検索に失敗しました: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>グループ検索</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <span>こんにちは、<?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>さん (ID: <?= htmlspecialchars($_SESSION['public_id'], ENT_QUOTES, 'UTF-8') ?>)</span>
            <a href="mypage.php">マイページ</a>
            <a href="logout.php">ログアウト</a>
        </div>

        <h1>グループ検索</h1>
        <?php if(isset($error_message)): ?>
            <p class="message error"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="group_search.php" method="get">
            <label for="query">グループ名またはIDで検索:</label>
            <input type="text" id="query" name="query" value="<?= htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') ?>" required>
            <button type="submit">検索</button>
        </form>

        <?php if (!empty($search_query) && count($search_results) > 0): ?>
            <h2>検索結果</h2>
            <ul>
                <?php foreach ($search_results as $group): ?>
                    <li>
                        <a href="group_profile.php?group_id=<?= htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?> (ID: <?= htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') ?>)
                        </a>
                        <p><?= nl2br(htmlspecialchars($group['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif (!empty($search_query) && count($search_results) == 0): ?>
            <p>検索条件に一致するグループは見つかりませんでした。</p>
        <?php endif; ?>

        <a href="mypage.php">マイページに戻る</a>
    </div>
</body>
</html>