<?php
session_start();
require 'db_connect.php';

$public_id = $_POST['public_id'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($public_id) || empty($password)) {
    header('Location: login_form.php?error=1');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE public_id = ?");
$stmt->execute([$public_id]);
$user = $stmt->fetch();

// ユーザーが存在し、かつパスワードが一致する場合
if ($user && password_verify($password, $user['password_hash'])) {
    // ログイン成功
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['public_id'] = $user['public_id']; // public_idをセッションに保存
    $_SESSION['my_hobbies'] = explode(',', $user['hobbies']); // 自分の趣味をセッションに保存
    header('Location: mypage.php');
    exit();
} else {
    // ログイン失敗
    header('Location: login_form.php?error=1');
    exit();
}