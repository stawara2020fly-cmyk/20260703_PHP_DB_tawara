const keywordInput = document.getElementById('keyword');
const searchBtn = document.getElementById('searchBtn');
const statusEl = document.getElementById('status');
const resultsEl = document.getElementById('results');

// タグチップをクリックしたら、そのキーワードで即検索する
document.querySelectorAll('.tag-chip').forEach(function (chip) {
  chip.addEventListener('click', function () {
    keywordInput.value = chip.dataset.tag;
    runSearch();
  });
});

searchBtn.addEventListener('click', runSearch);
keywordInput.addEventListener('keydown', function (e) {
  if (e.key === 'Enter') runSearch();
});

function runSearch() {
  const keyword = keywordInput.value.trim();

  // 入力チェック：空文字は検索しない
  if (keyword === '') {
    statusEl.textContent = 'キーワードを入力してください。';
    resultsEl.innerHTML = '';
    return;
  }

  statusEl.textContent = '検索中…';
  resultsEl.innerHTML = '';

  // PHP側のsearch.phpへ非同期で問い合わせる
  fetch('search.php?keyword=' + encodeURIComponent(keyword))
    .then(function (res) {
      if (!res.ok) throw new Error('サーバーエラー: ' + res.status);
      return res.json();
    })
    .then(renderResults)
    .catch(function (err) {
      statusEl.textContent = 'エラーが発生しました: ' + err.message;
    });
}

function renderResults(data) {
  if (!data.results || data.results.length === 0) {
    statusEl.textContent = '「' + data.keyword + '」に一致する条文は見つかりませんでした。';
    resultsEl.innerHTML = '<p class="empty">別のキーワードでお試しください。</p>';
    return;
  }

  statusEl.textContent = '「' + data.keyword + '」に一致する条文：' + data.results.length + '件';

  resultsEl.innerHTML = data.results.map(function (law) {
    const tagsHtml = law.tags.map(function (t) { return '<span>' + escapeHtml(t) + '</span>'; }).join('');

    let egovHtml = '';
    if (law.egov_text) {
      egovHtml = '<div class="egov-box">' +
        '<div class="egov-label">e-Gov法令APIより取得した正式な条文</div>' +
        escapeHtml(law.egov_text) +
        '<div><a class="egov-link" href="' + law.egov_url + '" target="_blank" rel="noopener">e-Govで原文を確認する</a></div>' +
        '</div>';
    } else if (law.egov_error) {
      egovHtml = '<div class="egov-box">e-Gov法令APIからの取得に失敗しました（' + escapeHtml(law.egov_error) + '）</div>';
    }

    return '<div class="result-card">' +
      '<div class="law-name">' + escapeHtml(law.law_name) + ' ' + escapeHtml(law.article_label) + '（' + escapeHtml(law.category) + '）</div>' +
      '<h3>' + escapeHtml(law.summary) + '</h3>' +
      '<div class="tags">' + tagsHtml + '</div>' +
      egovHtml +
      '</div>';
  }).join('');
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}