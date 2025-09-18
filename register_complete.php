<?php
// URLからIDを取得
$public_id = $_GET['id'] ?? '';

// IDが渡されていない場合はトップページに戻す
if (empty($public_id)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="completion-card">
            <h1>🎉 登録ありがとうございます！</h1>
            <p>あなたのIDが発行されました。ログイン時に必要ですので、必ず控えておいてください。</p>
            
            <div class="id-display">
                <p>あなたのID</p>
                <span><?= htmlspecialchars($public_id, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <a href="login_form.php" class="button">ログイン画面へ進む</a>
        </div>
    </div>
</body>
</html>