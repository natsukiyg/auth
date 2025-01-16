<?php
session_start();

// ログインしていない場合、login.php にリダイレクト
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 編集するメンバーIDをGETパラメータから取得
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
include("db_config.php"); // DB接続設定ファイル

// SQLでメンバー情報を取得
$sql = "SELECT * FROM db2_table WHERE memberId = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// メンバーが存在しない場合
if (!$member) {
    exit("該当するメンバーが見つかりません。");
}

// 編集後のデータを処理する
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // POSTデータを取得
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $facility = $_POST['facility'];

    //現在の日時を取得（更新日時として使用）
    $updated_at = date('Y-m-d H:i:s');

    // SQLを実行してデータを更新
    $sql = "UPDATE db2_table SET name = :name, gender = :gender, birthday = :birthday, 
            email = :email, address = :address, facility = :facility, updated_at = :updated_at
            WHERE memberId = :id";
    $stmt = $pdo->prepare($sql);

    // バインド変数を設定
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindValue(':birthday', $birthday, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address, PDO::PARAM_STR);
    $stmt->bindValue(':facility', $facility, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $updated_at, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        header("Location: read.php"); // 編集が成功したら、一覧ページにリダイレクト
        exit;
    } catch (PDOException $e) {
        echo "更新に失敗しました: " . $e->getMessage();
        exit;
    }
}

?>

<!-- 編集フォーム -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー情報編集</title>
    <link rel="stylesheet" href="./css/edit.css">
</head>
<body>

<h1>メンバー情報編集</h1>

<form action="edit.php?id=<?php echo $id; ?>" method="POST">
    <label for="name">名前:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required><br>

    <label for="gender">性別:</label>
    <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($member['gender']); ?>" required><br>

    <label for="birthday">誕生日:</label>
    <input type="text" id="birthday" name="birthday" value="<?php echo htmlspecialchars($member['birthday']); ?>" required><br>

    <label for="email">メールアドレス:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required><br>

    <label for="address">住所:</label>
    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($member['address']); ?>" required><br>

    <label for="facility">所属施設:</label>
    <input type="text" id="facility" name="facility" value="<?php echo htmlspecialchars($member['facility']); ?>" required><br>

    <!-- 知ったきっかけ（読み取り専用） -->
    <label for="whereDidYouHear">知ったきっかけ:</label>
    <input type="text" id="whereDidYouHear" name="whereDidYouHear" value="<?php echo htmlspecialchars($member['whereDidYouHear']); ?>" readonly><br>

    <!-- 期待する機能（読み取り専用） -->
    <label for="expectations">期待する機能:</label>
    <input type="text" id="expectations" name="expectations" value="<?php echo htmlspecialchars($member['expectations']); ?>" readonly><br>

    <input type="submit" value="更新">
</form>

</body>
</html>