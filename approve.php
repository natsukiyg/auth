<?php
include("db_config.php");

// 管理者だけがアクセスできるようにチェック（`user_role`が2なら管理者）
session_start();
if ($_SESSION['user_role'] != 2) {
    echo "アクセス権限がありません。";
    exit();
}

// 承認待ちユーザーを取得
try {
    $sql = "SELECT id, name, email, user_role FROM users WHERE is_approved = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>未承認ユーザー一覧</title>
    <link rel="stylesheet" href="./css/admin.css">
</head>
<body>

<h1>未承認ユーザー一覧</h1>

<!-- 承認待ちユーザーをテーブルで表示 -->
<table border="1">
    <tr>
        <th>名前</th>
        <th>メールアドレス</th>
        <th>権限</th>
        <th>操作</th>
    </tr>

    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo htmlspecialchars($user['name']); ?></td>
        <td><?php echo htmlspecialchars($user['email']); ?></td>
        <td>
            <?php
                if ($user['user_role'] == 1) echo "チームメンバー";
                else if ($user['user_role'] == 2) echo "管理者";
            ?>
        </td>
        <td>
            <!-- 承認・拒否ボタン -->
            <form action="approve_action.php" method="POST" style="display:inline;">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <button type="submit" name="action" value="approve">承認</button>
                <button type="submit" name="action" value="reject">拒否</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
