<?php
/**
 * admin_update.php
 * フォームから受け取った内容でDBのレコードを更新する。
 * 画面表示なし。完了後は admin_select.php にリダイレクト。
 */

// IDチェック
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id === 0) {
    header('Location: admin_select.php?error=' . urlencode('IDが指定されていません'));
    exit();
}

// 必須項目の入力チェック
if (
    !isset($_POST['law_name'])      || $_POST['law_name']      === '' ||
    !isset($_POST['law_id'])        || $_POST['law_id']        === '' ||
    !isset($_POST['article_label']) || $_POST['article_label'] === '' ||
    !isset($_POST['article_num'])   || $_POST['article_num']   === '' ||
    !isset($_POST['summary'])       || $_POST['summary']       === ''
) {
    header('Location: admin_edit.php?id=' . $id . '&error=' . urlencode('必須項目が未入力です'));
    exit();
}

// データ受け取り
$law_name      = $_POST['law_name'];
$law_id        = $_POST['law_id'];
$article_label = $_POST['article_label'];
$article_num   = $_POST['article_num'];
$category      = $_POST['category'] ?? '';
$tags          = $_POST['tags']     ?? '';
$summary       = $_POST['summary'];

// DB接続
require_once __DIR__ . '/db_connect.php';

// UPDATE文
$sql = 'UPDATE laws_table
        SET law_name      = :law_name,
            law_id        = :law_id,
            article_label = :article_label,
            article_num   = :article_num,
            category      = :category,
            tags          = :tags,
            summary       = :summary
        WHERE id = :id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':law_name',      $law_name,      PDO::PARAM_STR);
$stmt->bindValue(':law_id',        $law_id,        PDO::PARAM_STR);
$stmt->bindValue(':article_label', $article_label, PDO::PARAM_STR);
$stmt->bindValue(':article_num',   $article_num,   PDO::PARAM_STR);
$stmt->bindValue(':category',      $category,      PDO::PARAM_STR);
$stmt->bindValue(':tags',          $tags,          PDO::PARAM_STR);
$stmt->bindValue(':summary',       $summary,       PDO::PARAM_STR);
$stmt->bindValue(':id',            $id,            PDO::PARAM_INT);

try {
    $stmt->execute();
} catch (PDOException $e) {
    header('Location: admin_edit.php?id=' . $id . '&error=' . urlencode($e->getMessage()));
    exit();
}

// 更新成功 → 一覧画面に戻る
header('Location: admin_select.php?updated=1');
exit();
