<?php
// --- ご自身の環境に合わせて設定してください ---
$db_host = 'localhost';     // データベースのホスト名
$db_name = 'tetrocky_nb';  // データベース名
$db_user = 'tetrocky_gm';  // データベースのユーザー名
$db_pass = 'web2025games'; // データベースのパスワード
// -----------------------------------------

try {
    // データベースに接続
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
    // エラー発生時に例外を投げるように設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラーの場合はメッセージを出力して終了
    exit("データベースに接続できませんでした: " . $e->getMessage());
}