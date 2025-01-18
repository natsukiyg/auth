<?php

// db_config.phpからデータベース接続情報を持ってくる
include("db_config.php"); // db_config.phpの中身を読み込むので、$dbnや$pdoが使えるようになる

// 削除処理
if (isset($_POST['delete'])) {
    // 削除対象のIDを取得
    $id = $_POST['id'];

    // 削除操作: deleted_atに現在の日時をセット
    $sql = "UPDATE auth_table SET deleted_at = NOW() WHERE memberId = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    try {
        $status = $stmt->execute(); // 実行
    } catch (PDOException $e) {
        echo json_encode(["sql error" => "{$e->getMessage()}"]);
        exit();
    }

    // 削除後にリダイレクト
    header("Location: read.php");
    exit();
}

// SQL作成
$sql = 'SELECT * FROM auth_table WHERE deleted_at IS NULL'; // deleted_at が NULL のものだけ取得
$stmt = $pdo->prepare($sql);

//SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetchAllで全てのデータを配列で取得できる

// SQL実行の処理を関数化
$output = "";
foreach ($result as $record) {
  $output .= "
    <tr>
        <td>{$record["memberId"]}</td>
        <td>{$record["name"]}</td>
        <td>{$record["gender"]}</td>
        <td>{$record["birthday"]}</td>
        <td>{$record["email"]}</td>
        <td>{$record["address"]}</td>
        <td>{$record["facility"]}</td>
        <td>{$record["whereDidYouHear"]}</td>
        <td>{$record["expectations"]}</td>
        <td>{$record["registered_at"]}</td>
        <td>{$record["updated_at"]}</td>
        <td>
            <!-- 編集ボタン -->
            <a href='edit_profile.php?id={$record["memberId"]}'>編集</a> 
            <!-- 削除ボタン -->
            <form action='read.php' method='POST' onsubmit='return confirm(\"本当に削除しますか？\");' style='display:inline'>
                <input type='hidden' name='id' value='{$record["memberId"]}'>
                <input type='submit' name='delete' value='削除'>
            </form>
        </td>
    </tr>
  ";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー登録データ</title>
    <link rel="stylesheet" href="./css/read.css">
</head>

<body>
    <h1>メンバー登録データリスト</h1>
    <!-- 管理者ページへのリンク -->
    <div class="admin-link">
        <a href="admin.php" class="action-btn">管理者ページ</a>
    </div>
    <!-- 入力画面へのリンク -->
    <a href="index.php">入力画面へ戻る</a>

    <table>
        <thead>
            <tr>
                <!-- 登録データのヘッダーを表示 -->
                <th>会員番号</th>
                <th>氏名</th>
                <th>性別</th>
                <th>誕生日</th>
                <th>メールアドレス</th>
                <th>住所</th>
                <th>所属施設</th>
                <th>知ったきっかけ</th>
                <th>期待する機能</th>
                <th>登録日時</th>
                <th>更新日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <!--登録データが表示される-->
            <?= $output ?>
        </tbody>
    </table>
    <script>
        const memberData = <?= json_encode($result) ?>; //PHPからJSにデータを渡すために配列を変換
        console.log(memberData);
    </script>
</body>

</html>
