<?php
/**
 * admin_delete.php
 * 指定されたIDのレコードをDBから削除する。
 * 画面表示なし。完了後は admin_select.php にリダイレクト。
 */

// IDチェック
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id === 0) {
    header('Location: admin_select.php?error=' . urlencode('IDが指定されていません'));
    exit();
}

// DB接続
require_once __DIR__ . '/db_connect.php';

// DELETE文（WHEREで必ず1件だけ指定する）
$sql  = 'DELETE FROM laws_table WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

try {
    $stmt->execute();
} catch (PDOException $e) {
    header('Location: admin_select.php?error=' . urlencode($e->getMessage()));
    exit();
}

// 削除成功 → 一覧画面に戻る
header('Location: admin_select.php?deleted=1');
exit();
