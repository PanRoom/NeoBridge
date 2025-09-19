<?php
require 'db_connect.php';

try {
    // 既存のユーザーデータを取得
    $stmt_users = $pdo->query("SELECT id, hobbies FROM users WHERE hobbies IS NOT NULL AND hobbies != ''");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $user_id = $user['id'];
        $hobbies_string = $user['hobbies'];
        $hobbies_array = explode(',', $hobbies_string);

        foreach ($hobbies_array as $hobby_name) {
            $hobby_name = trim($hobby_name);
            if (empty($hobby_name)) continue;

            // カテゴリを仮で「その他」として扱うか、既存のカテゴリにマッピング
            // ここではシンプルに「その他」カテゴリを作成し、そこにアイテムを追加
            $category_name = 'その他'; // 仮のカテゴリ名

            // hobby_categories テーブルにカテゴリが存在するか確認し、なければ挿入
            $stmt_cat = $pdo->prepare("SELECT id FROM hobby_categories WHERE name = ?");
            $stmt_cat->execute([$category_name]);
            $category_id = $stmt_cat->fetchColumn();

            if (!$category_id) {
                $stmt_insert_cat = $pdo->prepare("INSERT INTO hobby_categories (name) VALUES (?)");
                $stmt_insert_cat->execute([$category_name]);
                $category_id = $pdo->lastInsertId();
            }

            // hobby_items テーブルにアイテムが存在するか確認し、なければ挿入
            $stmt_item = $pdo->prepare("SELECT id FROM hobby_items WHERE category_id = ? AND name = ?");
            $stmt_item->execute([$category_id, $hobby_name]);
            $hobby_item_id = $stmt_item->fetchColumn();

            if (!$hobby_item_id) {
                $stmt_insert_item = $pdo->prepare("INSERT INTO hobby_items (category_id, name) VALUES (?, ?)");
                $stmt_insert_item->execute([$category_id, $hobby_name]);
                $hobby_item_id = $pdo->lastInsertId();
            }

            // user_hobbies テーブルにユーザーと趣味アイテムの関連を挿入
            // 重複挿入を防ぐため、INSERT IGNORE を使用
            $stmt_user_hobby = $pdo->prepare("INSERT IGNORE INTO user_hobbies (user_id, hobby_item_id) VALUES (?, ?)");
            $stmt_user_hobby->execute([$user_id, $hobby_item_id]);
        }
    }

    echo "Hobby migration completed successfully.\n";

    // 移行後、usersテーブルからhobbiesカラムを削除
    // 注意: この操作は元に戻せません。データ移行が完全に成功したことを確認してから実行してください。
    // $pdo->exec("ALTER TABLE users DROP COLUMN hobbies");
    // echo "Column 'hobbies' dropped from 'users' table.\n";

} catch (PDOException $e) {
    echo "Error during hobby migration: " . $e->getMessage() . "\n";
    exit(1);
}
?>