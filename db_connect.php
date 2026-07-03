<?php
/**
 * db_connect.php
 * DB接続設定（共通）
 * 各PHPファイルから require_once で読み込んで使う
 */

$dbn  = 'mysql:dbname=shigeru67_gs_php_db;charset=utf8mb4;port=3306;host=mysql80.shigeru67.sakura.ne.jp';
$user = 'shigeru67_gs_php_db';
$pwd  = 'Yt_20030316';

try {
    $pdo = new PDO($dbn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['db error' => $e->getMessage()]);
    exit();
}
