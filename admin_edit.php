<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>条文データ修正 | 管理画面</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-form label {
      display: block;
      font-size: 13px;
      color: var(--text-sub);
      margin: 16px 0 4px;
    }
    .admin-form input[type="text"],
    .admin-form textarea {
      width: 100%;
      padding: 10px 12px;
      font-size: 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      outline: none;
      font-family: inherit;
    }
    .admin-form input[type="text"]:focus,
    .admin-form textarea:focus {
      border-color: var(--accent);
    }
    .admin-form textarea {
      height: 90px;
      resize: vertical;
    }
    .admin-nav {
      margin-bottom: 24px;
      font-size: 14px;
    }
    .admin-nav a {
      color: var(--accent);
      text-decoration: none;
      margin-right: 16px;
    }
    .hint {
      font-size: 12px;
      color: var(--text-sub);
      margin-top: 4px;
    }
  </style>
</head>
<body>
<div class="wrap">
  <header>
    <h1>条文データ修正</h1>
    <p>登録済みの条文データを編集します。</p>
  </header>

  <nav class="admin-nav">
    <a href="admin_select.php">← 一覧に戻る</a>
    <a href="index.html">検索画面へ</a>
  </nav>

<?php
// IDチェック
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    echo '<p style="color:red">IDが指定されていません。</p>';
    exit();
}

// DB接続
require_once __DIR__ . '/db_connect.php';

// 対象レコードを1件取得
$sql  = 'SELECT * FROM laws_table WHERE id = :id LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

try {
    $stmt->execute();
} catch (PDOException $e) {
    echo '<p style="color:red">SQLエラー：' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
}

$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) {
    echo '<p style="color:red">指定されたIDのデータが見つかりません。</p>';
    exit();
}
?>

  <?php if (!empty($_GET['error'])): ?>
    <div style="background:#fdecea;color:#b71c1c;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✗ エラーが発生しました：<?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <div class="search-card">
    <form class="admin-form" action="admin_update.php" method="POST">

      <!-- IDは隠しフィールドで送る -->
      <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">

      <label>法令名 <span style="color:red">*</span></label>
      <input type="text" name="law_name"
             value="<?= htmlspecialchars($r['law_name']) ?>" required>

      <label>法令ID（e-Gov用） <span style="color:red">*</span></label>
      <input type="text" name="law_id"
             value="<?= htmlspecialchars($r['law_id']) ?>" required>
      <p class="hint">e-Gov法令検索のURLから確認できます。</p>

      <label>条文ラベル <span style="color:red">*</span></label>
      <input type="text" name="article_label"
             value="<?= htmlspecialchars($r['article_label']) ?>" required>

      <label>条番号（数字のみ） <span style="color:red">*</span></label>
      <input type="text" name="article_num"
             value="<?= htmlspecialchars($r['article_num']) ?>" required>

      <label>カテゴリ</label>
      <input type="text" name="category"
             value="<?= htmlspecialchars($r['category']) ?>">

      <label>検索タグ（カンマ区切り）</label>
      <input type="text" name="tags"
             value="<?= htmlspecialchars($r['tags']) ?>">
      <p class="hint">カンマ（,）で区切って複数入力できます。</p>

      <label>条文の要約 <span style="color:red">*</span></label>
      <textarea name="summary" required><?= htmlspecialchars($r['summary']) ?></textarea>

      <div style="margin-top:20px;">
        <button type="submit">更新する</button>
      </div>

    </form>
  </div>
</div>
</body>
</html>
