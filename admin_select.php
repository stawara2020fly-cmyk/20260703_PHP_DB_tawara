<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録済み条文一覧 | 管理画面</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-nav {
      margin-bottom: 24px;
      font-size: 14px;
    }
    .admin-nav a {
      color: var(--accent);
      text-decoration: none;
      margin-right: 16px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      background: var(--card);
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--border);
    }
    th {
      background: var(--accent);
      color: #fff;
      padding: 10px 12px;
      text-align: left;
      font-weight: 500;
    }
    td {
      padding: 10px 12px;
      border-top: 1px solid var(--border);
      vertical-align: top;
    }
    tr:hover td {
      background: var(--accent-light);
    }
    .tag-chip {
      display: inline-block;
      background: var(--accent-light);
      color: var(--accent);
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 11px;
      margin: 2px 2px 0 0;
    }
    .count {
      font-size: 13px;
      color: var(--text-sub);
      margin-bottom: 16px;
    }
    .btn-edit {
      display: inline-block;
      padding: 4px 12px;
      font-size: 12px;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      margin-right: 6px;
    }
    .btn-delete {
      display: inline-block;
      padding: 4px 12px;
      font-size: 12px;
      background: #fff;
      color: #c0392b;
      border: 1px solid #c0392b;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-delete:hover { background: #fdecea; }
  </style>
</head>
<body>
<div class="wrap">
  <header>
    <h1>登録済み条文一覧</h1>
    <p>ナレッジベースに登録されている条文データです。</p>
  </header>

  <nav class="admin-nav">
    <a href="index.html">← 検索画面へ</a>
    <a href="admin_input.php">＋ 条文を追加する</a>
  </nav>

  <?php if (!empty($_GET['updated'])): ?>
    <div style="background:var(--accent-light);color:var(--accent);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✓ 条文データを更新しました。
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['deleted'])): ?>
    <div style="background:var(--accent-light);color:var(--accent);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✓ 条文データを削除しました。
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['error'])): ?>
    <div style="background:#fdecea;color:#b71c1c;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      ✗ エラーが発生しました：<?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

<?php
require_once __DIR__ . '/db_connect.php';

$sql  = 'SELECT * FROM laws_table ORDER BY id DESC';
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute();
} catch (PDOException $e) {
    echo '<p style="color:red">SQLエラー：' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
}

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count   = count($records);
?>

  <p class="count">全 <?= $count ?> 件登録されています。</p>

  <?php if ($count === 0): ?>
    <p style="color:var(--text-sub);text-align:center;padding:40px 0;">
      まだ条文データが登録されていません。<a href="admin_input.php" style="color:var(--accent)">追加する</a>
    </p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>法令名・条文</th>
          <th>カテゴリ</th>
          <th>タグ</th>
          <th>要約</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['id']) ?></td>
          <td>
            <?= htmlspecialchars($r['law_name']) ?><br>
            <span style="color:var(--accent);font-weight:500;"><?= htmlspecialchars($r['article_label']) ?></span>
          </td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td>
            <?php foreach (explode(',', $r['tags']) as $tag): ?>
              <?php if (trim($tag) !== ''): ?>
                <span class="tag-chip"><?= htmlspecialchars(trim($tag)) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          </td>
          <td><?= htmlspecialchars($r['summary']) ?></td>
          <td style="white-space:nowrap;">
            <a class="btn-edit" href="admin_edit.php?id=<?= $r['id'] ?>">編集</a>
            <form method="POST" action="admin_delete.php" style="display:inline;"
                  onsubmit="return confirm('ID<?= $r['id'] ?>「<?= htmlspecialchars($r['article_label']) ?>」を削除しますか？');">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button class="btn-delete" type="submit">削除</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</div>
</body>
</html>
