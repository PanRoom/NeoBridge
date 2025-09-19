<?php
session_start();
require 'db_connect.php';

$name = $_POST['name'] ?? '';
$password = $_POST['password'] ?? '';
$hobby_item_ids = $_POST['hobby_item_ids'] ?? []; // 選択された趣味アイテムのID
$new_hobby_name = $_POST['new_hobby_name'] ?? '';
$hobby_category_id = $_POST['hobby_category_id'] ?? '';

if (empty($name) || empty($password)) {
    exit('名前とパスワードは必須です。');
}

// 新しい趣味が入力された場合、hobby_itemsに追加
if (!empty($new_hobby_name)) {
    if (empty($hobby_category_id)) {
        exit('新しい趣味を追加する場合は、カテゴリを選択してください。');
    }
    try {
        // 既存のアイテムか確認
        $stmt_check_item = $pdo->prepare("SELECT id FROM hobby_items WHERE category_id = ? AND name = ?");
        $stmt_check_item->execute([$hobby_category_id, $new_hobby_name]);
        $existing_item_id = $stmt_check_item->fetchColumn();

        if (!$existing_item_id) {
            $stmt_insert_item = $pdo->prepare("INSERT INTO hobby_items (category_id, name) VALUES (?, ?)");
            $stmt_insert_item->execute([$hobby_category_id, $new_hobby_name]);
            $new_hobby_item_id = $pdo->lastInsertId();
            $hobby_item_ids[] = $new_hobby_item_id; // 新しい趣味をユーザーの趣味リストに追加
        } else {
            $hobby_item_ids[] = $existing_item_id; // 既存の趣味をユーザーの趣味リストに追加
        }
    } catch (PDOException $e) {
        exit("新しい趣味の登録に失敗しました: " . $e->getMessage());
    }
}

if (empty($hobby_item_ids)) {
    exit('趣味を一つ以上選択するか、新しい趣味を入力してください。');
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$public_id = substr(bin2hex(random_bytes(4)), 0, 6);

try {
    // usersテーブルにユーザーを挿入
    $stmt_user = $pdo->prepare(
        "INSERT INTO users (public_id, name, password_hash) VALUES (?, ?, ?)"
    );
    $stmt_user->execute([$public_id, $name, $password_hash]);
    $user_id = $pdo->lastInsertId();

    // user_hobbiesテーブルに趣味を挿入
    $stmt_user_hobbies = $pdo->prepare("INSERT INTO user_hobbies (user_id, hobby_item_id) VALUES (?, ?)");
    foreach ($hobby_item_ids as $item_id) {
        $stmt_user_hobbies->execute([$user_id, $item_id]);
    }

    // 新規登録ユーザーを自動ログインさせる
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['public_id'] = $public_id;
    // セッションに趣味アイテムIDを保存（後で詳細な趣味名を取得するために使用）
    $_SESSION['my_hobby_item_ids'] = $hobby_item_ids;

    // 直接マイページにリダイレクト
    header('Location: mypage.php');
    exit();

} catch (PDOException $e) {
    exit("登録に失敗しました: " . $e->getMessage());
}