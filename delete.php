<?php
session_start();

// ログインしていない場合、login.php にリダイレクト
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 削除するメンバーIDをGETパラメータから取得
if (!isset($_GET['id'])) {
    exit("メンバーIDが指定されていません。");
}

$id = $_GET['id'];

/* // DB接続
$dbn ='mysql:dbname=db2;charset=utf8mb4;port=3306;host=localhost'; //phpMyAdminのホスト名
$user = 'root';
$pwd = '';

try {
  $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
  echo json_encode(["db error" => "{$e->getMessage()}"]);
  exit();
} // 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．
 */
// DB接続設定
include("db_config.php");

// SQLでメンバー情報を削除
$sql = "DELETE FROM db2_table WHERE memberId = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

try {
    $stmt->execute();
    header("Location: read.php"); // 削除後は一覧ページにリダイレクト
    exit;
} catch (PDOException $e) {
    echo "削除に失敗しました: " . $e->getMessage();
    exit;
}
?>
