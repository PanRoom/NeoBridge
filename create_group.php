<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_name = $_POST['group_name'] ?? '';
$description = $_POST['description'] ?? '';

// 入力検証
if (empty($group_name)) {
    header('Location: create_group_form.php?error=' . urlencode('グループ名は必須です。'));
    exit();
}

try {
    // グループを作成
    $stmt = $pdo->prepare("INSERT INTO groups (name, description, creator_user_id) VALUES (?, ?, ?)");
    $stmt->execute([$group_name, $description, $user_id]);
    $group_id = $pdo->lastInsertId();

    // 作成者をグループメンバーに追加
    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
    $stmt->execute([$group_id, $user_id]);

    header('Location: create_group_form.php?status=success');
    exit();

} catch (PDOException $e) {
    header('Location: create_group_form.php?error=' . urlencode('グループの作成に失敗しました: ' . $e->getMessage()));
    exit();
}
?>