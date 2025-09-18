<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? '';

if (empty($group_id)) {
    header('Location: mypage.php?error=' . urlencode('グループIDが指定されていません。'));
    exit();
}

try {
    // ユーザーがすでにメンバーでないか確認
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt_check->execute([$group_id, $user_id]);
    if ($stmt_check->fetchColumn() > 0) {
        header('Location: group_profile.php?group_id=' . htmlspecialchars($group_id, ENT_QUOTES, 'UTF-8') . '&error=' . urlencode('あなたはすでにこのグループのメンバーです。'));
        exit();
    }

    // グループに参加
    $stmt_join = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
    $stmt_join->execute([$group_id, $user_id]);

    header('Location: group_profile.php?group_id=' . htmlspecialchars($group_id, ENT_QUOTES, 'UTF-8') . '&status=success');
    exit();

} catch (PDOException $e) {
    header('Location: group_profile.php?group_id=' . htmlspecialchars($group_id, ENT_QUOTES, 'UTF-8') . '&error=' . urlencode('グループへの参加に失敗しました: ' . $e->getMessage()));
    exit();
}
?>