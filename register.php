<?php
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

    // ★★★ 変更点 ★★★
    // ログインページではなく、IDを渡して登録完了ページにリダイレクトする
    header("Location: register_complete.php?id=" . $public_id);
    exit();

} catch (PDOException $e) {
    exit("登録に失敗しました: " . $e->getMessage());
}