<?php
session_start();
require 'db_connect.php';

$name = $_POST['name'] ?? '';
$password = $_POST['password'] ?? '';
$hobbies_array = $_POST['hobbies'] ?? [];

if (empty($name) || empty($password) || empty($hobbies_array)) {
    exit('名前、パスワード、趣味は必須です。');
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$hobbies_string = implode(',', $hobbies_array);
$public_id = substr(bin2hex(random_bytes(4)), 0, 6);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (public_id, name, hobbies, password_hash) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$public_id, $name, $hobbies_string, $password_hash]);

    // 新規登録ユーザーを自動ログインさせる
    $_SESSION['user_id'] = $pdo->lastInsertId(); // 挿入されたユーザーのIDを取得
    $_SESSION['user_name'] = $name;
    $_SESSION['public_id'] = $public_id;
    $_SESSION['my_hobbies'] = $hobbies_array;

    // 直接マイページにリダイレクト
    header('Location: mypage.php');
    exit();

} catch (PDOException $e) {
    exit("登録に失敗しました: " . $e->getMessage());
}