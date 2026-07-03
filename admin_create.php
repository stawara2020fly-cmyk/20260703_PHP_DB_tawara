<?php
/**
 * admin_create.php
 * フォームから受け取った条文データをDBに登録する。
 * 画面表示なし。完了後は admin_input.php にリダイレクト。
 */

// 必須項目の入力チェック
if (
    !isset($_POST['law_name'])     || $_POST['law_name']     === '' ||
    !isset($_POST['law_id'])       || $_POST['law_id']       === '' ||
    !isset($_POST['article_label'])|| $_POST['article_label']=== '' ||
    !isset($_POST['article_num'])  || $_POST['article_num']  === '' ||
    !isset($_POST['summary'])      || $_POST['summary']      === ''
) {
    header('Location: admin_input.php?error=' . urlencode('必須項目が未入力です'));
    exit();
}

// データ受け取り
$law_name     = $_POST['law_name'];
$law_id       = $_POST['law_id'];
$article_label= $_POST['article_label'];
$article_num  = $_POST['article_num'];
$category     = $_POST['category']  ?? '';
$tags         = $_POST['tags']      ?? '';
$summary      = $_POST['summary'];

// DB接続（共通設定を読み込む）
require_once __DIR__ . '/db_connect.php';

// SQL作成 & 実行
$sql = 'INSERT INTO laws_table
        (law_name, law_id, article_label, article_num, category, tags, summary)
        VALUES
        (:law_name, :law_id, :article_label, :article_num, :category, :tags, :summary)';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':law_name',      $law_name,      PDO::PARAM_STR);
$stmt->bindValue(':law_id',        $law_id,        PDO::PARAM_STR);
$stmt->bindValue(':article_label', $article_label, PDO::PARAM_STR);
$stmt->bindValue(':article_num',   $article_num,   PDO::PARAM_STR);
$stmt->bindValue(':category',      $category,      PDO::PARAM_STR);
$stmt->bindValue(':tags',          $tags,          PDO::PARAM_STR);
$stmt->bindValue(':summary',       $summary,       PDO::PARAM_STR);

try {
    $stmt->execute();
} catch (PDOException $e) {
    header('Location: admin_input.php?error=' . urlencode($e->getMessage()));
    exit();
}

// 登録成功 → 入力画面に戻る
header('Location: admin_input.php?success=1');
exit();
