<?php

// Composer でインストールした dotenv を読み込む
require 'vendor/autoload.php';

// .env ファイルを読み込む
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 環境に応じて接続設定を切り替え
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // localhost環境
    $dbHost = getenv('DB_HOST_LOCAL');
    $dbPort = getenv('DB_PORT_LOCAL');
    $dbName = getenv('DB_NAME_LOCAL');
    $dbUser = getenv('DB_USER_LOCAL');
    $dbPass = getenv('DB_PASS_LOCAL') ?: ''; // パスワードが設定されていない場合は空文字
} else {
    // 本番環境
    $dbHost = getenv('DB_HOST_PROD');
    $dbPort = getenv('DB_PORT_PROD');
    $dbName = getenv('DB_NAME_PROD');
    $dbUser = getenv('DB_USER_PROD');
    $dbPass = getenv('DB_PASS_PROD');
}

try {
    // DSN(Data Source Name): 接続情報
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4"; // データベース名を含む
    $pdo = new PDO($dsn, $dbUser, $dbPass);

    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // データベース接続成功の確認
    if (!$pdo) {
        die("データベース接続失敗");
    } else {
        // 接続が成功している場合は、このメッセージが表示されます
        echo "データベース接続成功！";
    }
} catch (PDOException $e) {
    // 接続エラーの場合
    echo "データベース接続エラー: " . $e->getMessage();
    exit();
}
?>
