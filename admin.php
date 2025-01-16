<?php
session_start();

// ログインしていない場合、login.php にリダイレクト
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ページ</title>
    <link rel="stylesheet" href="./css/admin.css">
</head>
<body>

<h1>管理者ページ</h1>
<p>ようこそ、管理者様！</p>
<p>以下のリンクから管理者機能にアクセスできます。</p>

<ul>
    <li><a href="index.php">メンバー登録フォームを見る</a></li>
    <li><a href="read.php">登録データリストを見る</a></li>
    <li><a href="deleted_list.php">削除されたデータリストを見る</a></li>
</ul>

<!-- ログアウトボタン -->
<form method="POST" action="logout.php">
    <button id="logout">
        <img src="img/logout.png" alt="logout" id="logout">
    </button>
</form>

</body>
</html>
