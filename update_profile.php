<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$hobbies_array = $_POST['hobbies'] ?? [];
$password = $_POST['password'] ?? '';

// 入力検証
if (empty($name) || empty($hobbies_array)) {
    header('Location: edit_profile_form.php?error=名前と趣味は必須です。');
    exit();
}

$hobbies_string = implode(',', $hobbies_array);

try {
    if (!empty($password)) {
        // パスワードが入力された場合、ハッシュ化して更新
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, hobbies = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$name, $hobbies_string, $password_hash, $user_id]);
    } else {
        // パスワードが入力されていない場合、名前と趣味のみ更新
        $stmt = $pdo->prepare("UPDATE users SET name = ?, hobbies = ? WHERE id = ?");
        $stmt->execute([$name, $hobbies_string, $user_id]);
    }

    // セッションのユーザー名と趣味も更新
    $_SESSION['user_name'] = $name;
    $_SESSION['my_hobbies'] = $hobbies_array;

    header('Location: edit_profile_form.php?status=success');
    exit();

} catch (PDOException $e) {
    header('Location: edit_profile_form.php?error=プロフィールの更新に失敗しました: ' . urlencode($e->getMessage()));
    exit();
}
