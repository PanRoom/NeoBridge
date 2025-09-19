<?php
session_start();
require 'db_connect.php';

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// データベースから現在のユーザー情報を取得
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // ユーザーが見つからない場合はログアウトしてエラーメッセージを表示
    session_destroy();
    header('Location: login_form.php?error=user_not_found');
    exit();
}

$current_name = $user['name'];

// ユーザーの現在の趣味アイテムIDを取得
$stmt_current_hobbies = $pdo->prepare("SELECT hobby_item_id FROM user_hobbies WHERE user_id = ?");
$stmt_current_hobbies->execute([$user_id]);
$current_hobby_item_ids = $stmt_current_hobbies->fetchAll(PDO::FETCH_COLUMN);

// 趣味カテゴリとアイテムを取得
$stmt_categories = $pdo->query("SELECT id, name FROM hobby_categories ORDER BY name");
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

$hobby_items_by_category = [];
foreach ($categories as $category) {
    $stmt_items = $pdo->prepare("SELECT id, name FROM hobby_items WHERE category_id = ? ORDER BY name");
    $stmt_items->execute([$category['id']]);
    $hobby_items_by_category[$category['id']] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>プロフィール編集</h1>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <p class="message success">プロフィールが更新されました！</p>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <p class="message error">エラーが発生しました: <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="update_profile.php" method="post">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($current_name, ENT_QUOTES, 'UTF-8') ?>" required>

            <label>趣味 (複数選択可):</label>
            <div class="hobby-selection">
                <?php foreach ($categories as $category): ?>
                    <div class="hobby-category-section">
                        <h3 class="category-header">
                            <input type="checkbox" class="category-checkbox" name="category_ids[]" value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>" data-category-id="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?> <span class="toggle-icon">+</span>
                        </h3>
                        <div class="hobby-options" style="display: none;">
                            <?php foreach ($hobby_items_by_category[$category['id']] as $item): ?>
                            <label><input type="checkbox" name="hobby_item_ids[]" value="<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>" <?= in_array($item['id'], $current_hobby_item_ids) ? 'checked' : '' ?>> <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <label for="newHobbyName">新しい趣味を追加:</label>
            <select id="newHobbyCategory" name="new_hobby_category_id">
                <option value="">カテゴリを選択</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <select id="newHobbyParentItem" name="new_hobby_parent_item_id" style="display: none;">
                <option value="">親となる趣味を選択 (オプション)</option>
            </select>
            <input type="text" id="newHobbyName" name="new_hobby_name" placeholder="例: ギター演奏">

            <label for="password">新しいパスワード (変更しない場合は空欄):</label>
            <input type="password" id="password" name="password">

            <button type="submit">更新</button>
        </form>
        <a href="mypage.php">マイページに戻る</a>
    </div>

    <script>
        console.log('Script block started!'); // 追加
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event fired in edit_profile_form.php!'); // 追加
            const categoryHeaders = document.querySelectorAll('.category-header');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            const hobbyItemCheckboxes = document.querySelectorAll('input[name="hobby_item_ids[]"]'); // 個別の趣味アイテムチェックボックス
            const newHobbyCategorySelect = document.getElementById('newHobbyCategory');
            const newHobbyParentItemSelect = document.getElementById('newHobbyParentItem');
            const newHobbyNameInput = document.getElementById('newHobbyName');

            const allHobbyItems = <?= json_encode($hobby_items_by_category) ?>;
            const currentHobbyItemIds = <?= json_encode($current_hobby_item_ids) ?>;

            categoryHeaders.forEach(header => {
                header.addEventListener('click', function(event) {
                    // チェックボックスのクリックは除外
                    if (event.target.classList.contains('category-checkbox')) {
                        return;
                    }
                    const hobbyOptions = this.nextElementSibling;
                    const toggleIcon = this.querySelector('.toggle-icon');

                    if (hobbyOptions.style.display === 'none') {
                        hobbyOptions.style.display = 'block';
                        toggleIcon.textContent = '-';
                    } else {
                        hobbyOptions.style.display = 'none';
                        toggleIcon.textContent = '+';
                    }
                });
            });

            categoryCheckboxes.forEach(checkbox => {
                // 自動選択ロジックを削除
                // checkbox.addEventListener('change', function() {
                //     const categoryId = this.dataset.categoryId;
                //     const hobbyOptionsDiv = this.closest('.hobby-category-section').querySelector('.hobby-options');
                //     const itemCheckboxes = hobbyOptionsDiv.querySelectorAll('input[type="checkbox"]');

                //     itemCheckboxes.forEach(itemCheckbox => {
                //         itemCheckbox.checked = this.checked;
                //     });
                // });
            });

            hobbyItemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Hobby item checkbox changed: ' + this.value + ', checked: ' + this.checked);
                });
            });

            newHobbyCategorySelect.addEventListener('change', function() {
                const selectedCategoryId = this.value;
                newHobbyParentItemSelect.innerHTML = '<option value="">親となる趣味を選択 (オプション)</option>';
                newHobbyParentItemSelect.style.display = 'none';

                if (selectedCategoryId) {
                    const items = allHobbyItems[selectedCategoryId];
                    if (items && items.length > 0) {
                        items.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.name;
                            newHobbyParentItemSelect.appendChild(option);
                        });
                        newHobbyParentItemSelect.style.display = 'block';
                    }
                }
            });

            // フォーム送信時のバリデーション
            document.querySelector('form').addEventListener('submit', function(event) {
                const selectedHobbies = document.querySelectorAll('input[name="hobby_item_ids[]"]:checked').length;
                const selectedCategories = document.querySelectorAll('.category-checkbox:checked').length; // カテゴリチェックボックスの選択数を取得
                const newHobbyName = newHobbyNameInput.value.trim();

                if (selectedHobbies === 0 && selectedCategories === 0 && newHobbyName === '') {
                    console.log('Validation failed: selectedHobbies=' + selectedHobbies + ', selectedCategories=' + selectedCategories + ', newHobbyName=' + newHobbyName);
                    alert('趣味を一つ以上選択するか、新しい趣味を入力してください。'); // アラートは残しておく
                    event.preventDefault();
                    return;
                }

                // 新しい趣味が入力された場合
                if (newHobbyName !== '') {
                    // 親アイテムが選択されていない場合、カテゴリが必須
                    if (!newHobbyParentItem && !newHobbyCategory) {
                        alert('新しい趣味を追加する場合は、カテゴリを選択してください。');
                        event.preventDefault();
                        return;
                    }
                }
            });
        });
    </script>
</body>