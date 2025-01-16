<?php
// セッション開始
session_start();

// POSTデータ確認 ()内にダメな条件を書く
if(!isset($_POST["name"]) || $_POST["name"] === "" ||
   !isset($_POST["gender"]) || $_POST["gender"] === "" ||
   !isset($_POST["birthday"]) || $_POST["birthday"] === "" ||
   !isset($_POST["email"]) || $_POST["email"] === "" ||
   !isset($_POST["address"]) || $_POST["address"] === "" ||
   !isset($_POST["facility"]) || $_POST["facility"] === "" ||
   !isset($_POST["whereDidYouHear"]) || $_POST["whereDidYouHear"] === "" ||
   !isset($_POST["expectations"]) || $_POST["expectations"] === "" ) { // いずれかが空の場合
    exit("データがありません");
}

// POSTデータ取得
$name = $_POST["name"];
$gender = $_POST["gender"];
$birthday = $_POST["birthday"];
$email = $_POST["email"];
$address = $_POST["address"];
$facility = $_POST["facility"];
$whereDidYouHear = $_POST["whereDidYouHear"];
$expectations = $_POST["expectations"];

//バリデーション：メールアドレスの形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  exit("無効なメールアドレスです");
}

//バリデーション：誕生日の形式チェック
if (!strtotime($birthday)) {
  exit("誕生日を正しい形式で入力してください");
}

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
// db_config.phpからデータベース接続情報を持ってくる
include("db_config.php"); // db_config.phpの中身を読み込むので、$dbnや$pdoが使えるようになる

// SQL作成&実行
$sql = 'INSERT INTO db2_table (memberId, name, gender, birthday, email, address, facility, whereDidYouHear, expectations, registered_at, updated_at) VALUES (NULL, :name, :gender, :birthday, :email, :address, :facility, :whereDidYouHear, :expectations, now(), now())';
//SQLインジェクションを起こさないために「:name」や「:gender」（バインド変数）を使っているところ
$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
$stmt->bindValue(':birthday', $birthday, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':address', $address, PDO::PARAM_STR);
$stmt->bindValue(':facility', $facility, PDO::PARAM_STR);
$stmt->bindValue(':whereDidYouHear', $whereDidYouHear, PDO::PARAM_STR);
$stmt->bindValue(':expectations', $expectations, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute(); //実行ボタンを押して、うまくいったかいってないかを判断する
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// 登録データをSESSIONに保存(GETパラメータよりセキュリティが高いSESSIONを使う)
$_SESSION['member_data'] = [
  'name' => $name,
  'gender' => $gender,
  'birthday' => $birthday,
  'email' => $email,
  'address' => $address,
  'facility' => $facility,
  'whereDidYouHear' => $whereDidYouHear,
  'expectations' => $expectations
];

//データ挿入後、確認ページへリダイレクト
header("Location: confirmation.php");
exit(); // これ以下の処理を行わない