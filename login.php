<?php
session_start(); // セッション開始

// 管理者のハッシュ化されたパスワード（本来、データベースなどから取得すべきです）
define('ADMIN_PASSWORD_HASH', '$2y$10$6sB6yqy6noPDpgPYc6sFZeXo.zIPag5fMKhp7NijKZnQqF.986XBO'); // パスワード のハッシュ

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? ''; // パスワード取得

    // パスワード確認（ハッシュ化されたパスワードと照合）
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        // パスワードが一致した場合、セッションにフラグをセット
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php'); // 管理者ページにリダイレクト
        exit;
    } else {
        $error_message = "パスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>

<h2>管理者ログイン</h2>
<?php
 if (isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } 
?>

<form method="POST">
    <input type="password" name="password" id="password" placeholder="パスワードを入力" required>
    <button type="submit">ログイン</button>
</form>

</body>
</html>
