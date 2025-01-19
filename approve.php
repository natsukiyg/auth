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
    $sql = "SELECT memberId, name, email, facility, user_role, registered_at FROM auth_table WHERE is_approved = 0";
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
    <script type="text/javascript">
        // 承認・拒否時に確認ポップアップを表示
        function confirmAction(userName, facilityName, userRole, action) {
            let role = (userRole == 1) ? "チームメンバー" : "管理者";
            let actionText = (action == 'approve') ? "承認" : "拒否";
            let message = userName + " さん（所属施設名：" + facilityName + "）の " + role + " を " + actionText + " しますか？";

            return confirm(message);  // ユーザーがOKを押した場合のみ送信
        }
    </script>
</head>
<body>

<h1>未承認ユーザー一覧</h1>

<!-- 承認待ちユーザーをテーブルで表示 -->
<table border="1">
    <tr>
        <th>名前</th>
        <th>メールアドレス</th>
        <th>所属施設</th>
        <th>権限</th>
        <th>登録日時</th>
        <th>操作</th>
    </tr>

    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo htmlspecialchars($user['name']); ?></td>
        <td><?php echo htmlspecialchars($user['email']); ?></td>
        <td>
            <?php echo htmlspecialchars($user['facility']) ? htmlspecialchars($user['facility']) : "未設定"; ?>
        </td>
        <td>
            <?php
                if ($user['user_role'] == 1) echo "チームメンバー";
                else if ($user['user_role'] == 2) echo "管理者";
            ?>
        </td>
        <td>
            <!-- 承認・拒否ボタン -->
            <form action="approve_action.php" method="POST" style="display:inline;">
                <input type="hidden" name="user_id" value="<?php echo $user['memberId']; ?>">
                
                <!-- 承認ボタン -->
                <button type="submit" name="action" value="approve" onclick="return confirmAction('<?php echo addslashes($user['name']); ?>', '<?php echo addslashes($user['facility']); ?>', <?php echo $user['user_role']; ?>, 'approve');">
                    承認
                </button>

                <!-- 拒否ボタン -->
                <button type="submit" name="action" value="reject" onclick="return confirmAction('<?php echo addslashes($user['name']); ?>', '<?php echo addslashes($user['facility']); ?>', <?php echo $user['user_role']; ?>, 'reject');">
                    拒否
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>