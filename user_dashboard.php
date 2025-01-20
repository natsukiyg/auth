<?php
session_start(); // セッション開始

// ログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ユーザー情報を取得
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$is_approved = $_SESSION['is_approved'];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー用ダッシュボード</title>
    <link rel="stylesheet" href="./css/user_dashboard.css">
</head>
<body>

<h1>ようこそ！ <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>さん</h1>

<p>あなたの権限: 
<?php
    if ($user_role == 0) {
        echo "スタッフ（閲覧のみ）";
    } elseif ($user_role == 1) {
        echo "チームメンバー（編集可能）";
    } elseif ($user_role == 2) {
        echo "管理者";
    }
?>
</p>

<!-- 役割に応じたリンクを表示 -->
<?php if ($is_approved == 1): ?>
    <p>あなたは承認されています。</p>

    <!-- すべてのユーザーにプロフィール編集リンクを表示 -->
    <p><a href="edit_profile.php?id=<?php echo $_SESSION['user_id']; ?>">プロフィール編集</a></p>

    <!-- user_role 2の場合、管理者リンク -->
    <?php if ($user_role == 2): ?>
        <p><a href="admin.php">管理者ページ</a></p>
    <?php endif; ?>

<?php else: ?>
    <p>あなたのアカウントは承認待ちです。</p>
<?php endif; ?>

<!-- ログアウトボタン -->
<form method="POST" action="logout.php">
    <button id="logout-btn" style="background: transparent; border: none; padding: 0;">
        <img src="img/logout.png" alt="logout" id="logout-img">
    </button>
</form>

</body>
</html>
