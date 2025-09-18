<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
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
            <div class="hobby-options">
                <label><input type="checkbox" name="hobbies[]" value="映画"> 映画</label>
                <label><input type="checkbox" name="hobbies[]" value="音楽"> 音楽</label>
                <label><input type="checkbox" name="hobbies[]" value="スポーツ"> スポーツ</label>
                <label><input type="checkbox" name="hobbies[]" value="読書"> 読書</label>
            </div>
            <button type="submit">登録してIDを発行</button>
        </form>
        <a href="index.php">トップに戻る</a>
    </div>
</body>
</html>