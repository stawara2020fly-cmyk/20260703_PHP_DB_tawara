<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>条文データ登録 | 管理画面</title>
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
    <h1>条文データ登録</h1>
    <p>ナレッジベースに条文データを追加します。</p>
  </header>

  <nav class="admin-nav">
    <a href="index.html">← 検索画面へ</a>
    <a href="admin_select.php">登録済み一覧を見る</a>
  </nav>

  <?php if (!empty($_GET['success'])): ?>
    <div style="background:var(--accent-light);color:var(--accent);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✓ 条文データを登録しました。
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['error'])): ?>
    <div style="background:#fdecea;color:#b71c1c;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✗ エラーが発生しました：<?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <div class="search-card">
    <form class="admin-form" action="admin_create.php" method="POST">

      <label>法令名 <span style="color:red">*</span></label>
      <input type="text" name="law_name" placeholder="例：宅地建物取引業法" required>

      <label>法令ID（e-Gov用） <span style="color:red">*</span></label>
      <input type="text" name="law_id" placeholder="例：327AC1000000176" required>
      <p class="hint">e-Gov法令検索のURLから確認できます。</p>

      <label>条文ラベル <span style="color:red">*</span></label>
      <input type="text" name="article_label" placeholder="例：第35条" required>

      <label>条番号（数字のみ） <span style="color:red">*</span></label>
      <input type="text" name="article_num" placeholder="例：35" required>

      <label>カテゴリ</label>
      <input type="text" name="category" placeholder="例：不動産・宅建">

      <label>検索タグ（カンマ区切り）</label>
      <input type="text" name="tags" placeholder="例：重要事項説明,契約前,購入者保護">
      <p class="hint">キーワード検索の対象になります。カンマ（,）で区切って複数入力できます。</p>

      <label>条文の要約 <span style="color:red">*</span></label>
      <textarea name="summary" placeholder="条文の内容・趣旨を簡潔に記述してください。" required></textarea>

      <div style="margin-top:20px;">
        <button type="submit">登録する</button>
      </div>

    </form>
  </div>
</div>
</body>
</html>
