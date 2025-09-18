<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? '';

if (empty($group_id)) {
    header('Location: mypage.php?error=' . urlencode('グループIDが指定されていません。'));
    exit();
}

try {
    // グループ情報を取得
    $stmt_group = $pdo->prepare("SELECT id, name, description, creator_user_id FROM groups WHERE id = ?");
    $stmt_group->execute([$group_id]);
    $group = $stmt_group->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        header('Location: mypage.php?error=' . urlencode('グループが見つかりませんでした。'));
        exit();
    }

    // ユーザーがこのグループのメンバーであるか確認
    $stmt_is_member = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt_is_member->execute([$group_id, $user_id]);
    $is_member = ($stmt_is_member->fetchColumn() > 0);

    // グループメンバーを取得
    $stmt_members = $pdo->prepare("SELECT u.id, u.name, u.public_id, u.hobbies FROM users u JOIN group_members gm ON u.id = gm.user_id WHERE gm.group_id = ? ORDER BY u.id = ? DESC, u.name ASC");
    $stmt_members->execute([$group_id, $user_id]);
    $members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);

    // ログインユーザーの趣味をセッションから取得
    $my_hobbies = $_SESSION['my_hobbies'] ?? [];

} catch (PDOException $e) {
    header('Location: mypage.php?error=' . urlencode('グループ情報の取得に失敗しました: ' . $e->getMessage()));
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?> - グループプロフィール</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <span>こんにちは、<?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>さん (ID: <?= htmlspecialchars($_SESSION['public_id'], ENT_QUOTES, 'UTF-8') ?>)</span>
            <a href="mypage.php">マイページ</a>
            <a href="logout.php">ログアウト</a>
        </div>

        <h1><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= nl2br(htmlspecialchars($group['description'], ENT_QUOTES, 'UTF-8')) ?></p>

        <?php if (!$is_member): ?>
            <form action="join_group.php" method="post">
                <input type="hidden" name="group_id" value="<?= htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="button">このグループに参加する</button>
            </form>
        <?php else: ?>
            <p class="message success">あなたはすでにこのグループのメンバーです。</p>
        <?php endif; ?>

        <h2>メンバー</h2>
        <?php if (count($members) > 0): ?>
            <ul>
                <?php foreach ($members as $member): ?>
                    <li class="<?= ($member['id'] == $user_id) ? 'self-member' : '' ?>">
                        <a href="profile.php?id=<?= htmlspecialchars($member['public_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?> (ID: <?= htmlspecialchars($member['public_id'], ENT_QUOTES, 'UTF-8') ?>)
                        </a>
                        <?php
                            $member_hobbies = explode(',', $member['hobbies']);
                            if ($member['id'] == $user_id) {
                                // ログインユーザー自身の場合、共通の趣味は表示しない
                                // 趣味リストはmypage.phpで表示されるため、ここでは省略
                            } else {
                                $common_hobbies = array_intersect($my_hobbies, $member_hobbies);
                                if (!empty($common_hobbies)) {
                                    echo ' <span class="common-hobbies"> (共通の趣味: ' . htmlspecialchars(implode(', ', $common_hobbies), ENT_QUOTES, 'UTF-8') . ')</span>';
                                } else {
                                    echo ' <span class="no-common-hobbies"> (共通の趣味はありません)</span>';
                                }
                            }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>まだメンバーがいません。</p>
        <?php endif; ?>

        <a href="mypage.php">マイページに戻る</a>
    </div>
</body>
</html>