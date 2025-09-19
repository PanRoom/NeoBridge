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

            <label>趣味 (複数選択可):</label>
            <div class="hobby-selection">
                <select id="hobbyCategory" name="hobby_category_id">
                    <option value="">カテゴリを選択</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="hobbyItemsContainer" class="hobby-options">
                    <!-- 選択されたカテゴリの趣味アイテムがここに動的にロードされます -->
                </div>
            </div>

            <label for="newHobbyName">新しい趣味を追加 (カテゴリ選択後):</label>
            <input type="text" id="newHobbyName" name="new_hobby_name" placeholder="例: ギター演奏">

            <button type="submit">登録してIDを発行</button>
        </form>
        <a href="index.php">トップに戻る</a>
    </div>
    <script>
        const hobbyCategorySelect = document.getElementById('hobbyCategory');
        const hobbyItemsContainer = document.getElementById('hobbyItemsContainer');
        const newHobbyNameInput = document.getElementById('newHobbyName');

        const allHobbyItems = <?= json_encode($hobby_items_by_category) ?>;

        function updateHobbyItems() {
            const selectedCategoryId = hobbyCategorySelect.value;
            hobbyItemsContainer.innerHTML = ''; // クリア

            if (selectedCategoryId) {
                const items = allHobbyItems[selectedCategoryId];
                if (items && items.length > 0) {
                    items.forEach(item => {
                        const label = document.createElement('label');
                        label.innerHTML = `<input type="checkbox" name="hobby_item_ids[]" value="${item.id}"> ${item.name}`;
                        hobbyItemsContainer.appendChild(label);
                    });
                } else {
                    hobbyItemsContainer.innerHTML = '<p>このカテゴリにはまだ趣味がありません。</p>';
                }
            }
        }

        hobbyCategorySelect.addEventListener('change', updateHobbyItems);
        updateHobbyItems(); // 初期ロード

        // フォーム送信時のバリデーション
        document.querySelector('form').addEventListener('submit', function(event) {
            const selectedHobbies = document.querySelectorAll('input[name="hobby_item_ids[]"]:checked').length;
            const newHobbyName = newHobbyNameInput.value.trim();
            const selectedCategoryId = hobbyCategorySelect.value;

            if (selectedHobbies === 0 && newHobbyName === '') {
                alert('趣味を一つ以上選択するか、新しい趣味を入力してください。');
                event.preventDefault();
                return;
            }

            if (newHobbyName !== '' && !selectedCategoryId) {
                alert('新しい趣味を追加する場合は、カテゴリを選択してください。');
                event.preventDefault();
                return;
            }
        });
    </script>
    <script src="script.js"></script>
</body>
</html>