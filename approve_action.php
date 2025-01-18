<?php
include("db_config.php");

// 管理者だけがアクセスできるようにチェック（`user_role`が2なら管理者）
session_start();
if ($_SESSION['user_role'] != 2) {
    echo "アクセス権限がありません。";
    exit();
}

// ボタンが押された場合に処理を実行
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    try {
        if ($action == 'approve') {
            // 承認
            $sql = "UPDATE users SET is_approved = 1 WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo "ユーザーの承認が完了しました。";
        } elseif ($action == 'reject') {
            // 拒否（ユーザー削除）
            $sql = "DELETE FROM users WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo "ユーザーの登録が拒否されました。";
        }

        // 承認後、または拒否後に管理者ページにリダイレクト
        header("Location: approve.php");
        exit();

    } catch (PDOException $e) {
        echo json_encode(["db error" => "{$e->getMessage()}"]);
        exit();
    }
}
?>
