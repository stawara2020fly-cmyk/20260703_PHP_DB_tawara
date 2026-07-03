<?php
/**
 * search.php
 *
 * DBのlaws_tableをキーワードで検索し、
 * ヒットした条文の中から1件についてe-Gov法令APIから正式条文を取得して返す。
 *
 * 前回（週次課題1）はdata/laws.jsonを参照していたが、
 * 今回（週次課題2）からDBのSELECT文に切り替えた。
 */

header('Content-Type: application/json; charset=utf-8');

// ---- 1. 入力チェック ----
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if ($keyword === '') {
    echo json_encode(['keyword' => '', 'results' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

// ---- 2. DB接続 ----
require_once __DIR__ . '/db_connect.php';

// ---- 3. キーワード検索（summary・tags・article_labelを対象に部分一致） ----
$sql = 'SELECT * FROM laws_table
        WHERE summary      LIKE :kw
           OR tags         LIKE :kw
           OR article_label LIKE :kw
           OR law_name     LIKE :kw
        ORDER BY id ASC';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kw', '%' . $keyword . '%', PDO::PARAM_STR);

try {
    $stmt->execute();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

$matched = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---- 4. ヒットした最初の1件についてe-Gov法令APIから正式条文を取得 ----
if (count($matched) > 0) {
    $egov = fetchEgovArticle($matched[0]['law_id'], $matched[0]['article_num']);
    if ($egov['success']) {
        $matched[0]['egov_text'] = $egov['text'];
        $matched[0]['egov_url']  = $egov['url'];
    } else {
        $matched[0]['egov_error'] = $egov['error'];
        $matched[0]['egov_url']   = $egov['url'];
    }
}

// tagsを配列に変換してJSが扱いやすくする
foreach ($matched as &$row) {
    $row['tags'] = array_map('trim', explode(',', $row['tags']));
}
unset($row);

echo json_encode([
    'keyword' => $keyword,
    'results' => $matched,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


/**
 * e-Gov 法令API（Version 1）から指定条文の本文を取得する。
 */
function fetchEgovArticle(string $lawId, string $article): array
{
    $apiUrl  = "https://laws.e-gov.go.jp/api/1/articles;lawId={$lawId};article={$article}";
    $viewUrl = "https://laws.e-gov.go.jp/law/{$lawId}#Mp-At_{$article}";

    $xmlString = httpGet($apiUrl);
    if ($xmlString === null) {
        return ['success' => false, 'error' => 'APIへの接続に失敗しました', 'url' => $viewUrl];
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlString);
    if ($xml === false) {
        return ['success' => false, 'error' => '条文データの解析に失敗しました', 'url' => $viewUrl];
    }

    $code = (string)($xml->Result->Code ?? '1');
    if ($code !== '0') {
        $message = (string)($xml->Result->Message ?? '不明なエラー');
        return ['success' => false, 'error' => $message ?: '取得に失敗しました', 'url' => $viewUrl];
    }

    $sentences = $xml->xpath('//LawContents//Sentence');
    if (empty($sentences)) {
        return ['success' => false, 'error' => '条文本文が見つかりませんでした', 'url' => $viewUrl];
    }

    $text = '';
    foreach ($sentences as $s) {
        $text .= (string)$s;
    }

    return ['success' => true, 'text' => $text, 'url' => $viewUrl];
}

/**
 * file_get_contents → cURL のフォールバック付きGETリクエスト
 */
function httpGet(string $url): ?string
{
    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(['http' => ['timeout' => 8]]);
        $result  = @file_get_contents($url, false, $context);
        if ($result !== false) return $result;
    }
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $result = curl_exec($ch);
        $ok     = ($result !== false && curl_errno($ch) === 0);
        curl_close($ch);
        if ($ok) return $result;
    }
    return null;
}
