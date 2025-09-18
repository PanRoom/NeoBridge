<?php
session_start();

// セッション変数を全て解除
$_SESSION = array();

// セッションを破壊
session_destroy();

// タイトルページにリダイレクト
header('Location: index.php');
exit();