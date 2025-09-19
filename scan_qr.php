<?php
session_start();

// ログインしていない場合はログインページに戻す
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRコードをスキャン</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        #qr-reader {
            width: 100%;
            max-width: 400px;
            margin: 2em auto;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        #qr-reader__dashboard_section_csr {
            display: none; /* カメラ選択UIを非表示 */
        }
        #qr-reader__scan_region {
            border-radius: 8px;
        }
        .result-message {
            text-align: center;
            margin-top: 1em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span>こんにちは、<?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>さん (ID: <?= htmlspecialchars($_SESSION['public_id'], ENT_QUOTES, 'UTF-8') ?>)</span>
            <a href="mypage.php">マイページ</a>
            <a href="logout.php">ログアウト</a>
        </div>

        <h1>QRコードをスキャン</h1>
        <p>カメラでQRコードを読み取ってください。</p>

        <div id="qr-reader"></div>
        <div id="qr-reader-results" class="result-message"></div>

        <a href="mypage.php">マイページに戻る</a>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Handle on success condition with the decoded message.
            console.log(`Scan result: ${decodedText}`, decodedResult);
            document.getElementById('qr-reader-results').innerHTML = `QRコードを検出しました: <a href="${decodedText}">${decodedText}</a>`;
            // プロフィールURLであればリダイレクト
            if (decodedText.includes('profile.php?id=')) {
                window.location.href = decodedText;
            } else {
                alert('プロフィールURLではないQRコードがスキャンされました。');
            }
            html5QrcodeScanner.clear();
        }

        function onScanError(errorMessage) {
            // handle on error condition, with error message
            // console.warn(`QR Code Scan Error: ${errorMessage}`);
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
</body>
</html>