<?php
require 'db_connect.php';

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
    <title>新規登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>新規登録</h1>
        <form action="register.php" method="post">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <label for="bio">自己紹介:</label>
            <textarea id="bio" name="bio" rows="4" placeholder="趣味や好きなことなど自由にご記入ください。"></textarea>


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
                            <label><input type="checkbox" name="hobby_item_ids[]" value="<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>"> <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></label>
                        <?php endforeach; ?>
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

            <button type="submit">登録してIDを発行</button>
        </form>
        <a href="index.php">トップに戻る</a>
    </div>
    <script>
        console.log('Script block started!'); // 追加
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event fired in register_form.php!'); // 追加
            const categoryHeaders = document.querySelectorAll('.category-header');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            const hobbyItemCheckboxes = document.querySelectorAll('input[name="hobby_item_ids[]"]'); // 個別の趣味アイテムチェックボックス
            const newHobbyCategorySelect = document.getElementById('newHobbyCategory');
            const newHobbyParentItemSelect = document.getElementById('newHobbyParentItem');
            const newHobbyNameInput = document.getElementById('newHobbyName');

            const allHobbyItems = <?= json_encode($hobby_items_by_category) ?>;

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
                    console.log('CLIENT-SIDE VALIDATION FAILED!'); // New log
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
    <script src="script.js"></script>
</body>
</html>