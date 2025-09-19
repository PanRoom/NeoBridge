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
$hobby_item_ids = $_POST['hobby_item_ids'] ?? []; // 選択された趣味アイテムのID
$new_hobby_name = $_POST['new_hobby_name'] ?? '';
$hobby_category_id = $_POST['hobby_category_id'] ?? '';
$password = $_POST['password'] ?? '';

// 入力検証
if (empty($name)) {
    header('Location: edit_profile_form.php?error=' . urlencode('名前は必須です。'));
    exit();
}

// 新しい趣味が入力された場合、hobby_itemsに追加
if (!empty($new_hobby_name)) {
    if (empty($hobby_category_id)) {
        header('Location: edit_profile_form.php?error=' . urlencode('新しい趣味を追加する場合は、カテゴリを選択してください。'));
        exit();
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
        header('Location: edit_profile_form.php?error=' . urlencode('新しい趣味の登録に失敗しました: ' . $e->getMessage()));
        exit();
    }
}

if (empty($hobby_item_ids)) {
    header('Location: edit_profile_form.php?error=' . urlencode('趣味を一つ以上選択するか、新しい趣味を入力してください。'));
    exit();
}

try {
    // usersテーブルのユーザー名を更新
    $stmt_user_update = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt_user_update->execute([$name, $user_id]);

    // パスワードが入力された場合、ハッシュ化して更新
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt_password_update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt_password_update->execute([$password_hash, $user_id]);
    }

    // 既存のuser_hobbiesを削除
    $stmt_delete_hobbies = $pdo->prepare("DELETE FROM user_hobbies WHERE user_id = ?");
    $stmt_delete_hobbies->execute([$user_id]);

    // 新しいuser_hobbiesを挿入
    $stmt_insert_hobbies = $pdo->prepare("INSERT INTO user_hobbies (user_id, hobby_item_id) VALUES (?, ?)");
    foreach ($hobby_item_ids as $item_id) {
        $stmt_insert_hobbies->execute([$user_id, $item_id]);
    }

    // セッションのユーザー名と趣味も更新
    $_SESSION['user_name'] = $name;
    $_SESSION['my_hobby_item_ids'] = $hobby_item_ids;

    header('Location: edit_profile_form.php?status=success');
    exit();

} catch (PDOException $e) {
    header('Location: edit_profile_form.php?error=' . urlencode('プロフィールの更新に失敗しました: ' . $e->getMessage()));
    exit();
}
