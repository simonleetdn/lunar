<?php include 'metaseo.php'; ?>
<meta name="description" content="傳統民俗周公解夢關鍵字檢索，輸入夢境關鍵字或依人物、動物、自然天象、生活器物分類快速查詢夢境吉凶解析。">
<link rel="canonical" href="https://lunar.ioi.tw/dream.php" />
<title>周公解夢關鍵字檢索<?php echo isset($_GET['q']) && trim($_GET['q']) !== '' ? "：夢見".htmlspecialchars(trim($_GET['q'])) : ''; ?></title>
<?php include 'head.php'; ?>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-danger">
	  <div class="container-md">
    <a class="navbar-brand" href="lunardate.php">
		<img src="square.png" width="32" height="32" class="d-inline-block align-top" alt="洛書">
		農民曆
	</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
		 <li class="nav-item">
          <a id="lunar" class="nav-link" href="lunardate.php">每月農民曆</a>
        </li>
		  <li class="nav-item">
          <a id="yidate" class="nav-link" href="yidate.php">每年宜日速查</a>
        </li>
		 <li class="nav-item">
            <a id="calendar" class="nav-link" href="/calendar/">日曆(自動更新)</a>
          </li>
           <li class="nav-item">
             <a id="ziwei" class="nav-link" href="ziwei.php">紫微斗數</a>
           </li> 
      </ul>
    </div>
	</div>	  
  </nav>
	<div class="container">
    <div class="row">
      <div class="col-md-12">
		  <h2>周公解夢關鍵字檢索</h2>
<?php
$dreamData = [
    // 天象自然
    ['cat' => '天象自然', 'key' => '太陽、日出', 'omen' => '大吉', 'desc' => '夢見太陽升起或陽光普照，主事業興旺、貴人相助，孕婦夢之主生貴子。'],
    ['cat' => '天象自然', 'key' => '月亮、滿月', 'omen' => '吉', 'desc' => '夢見明月當空，主家庭和睦、感情圓滿；若月缺或被雲遮，則防事有波折。'],
    ['cat' => '天象自然', 'key' => '下雨、大雨', 'omen' => '吉', 'desc' => '夢見甘霖大雨，主財運亨通、煩惱消除；若淋雨狼狽，則防小人是非。'],
    ['cat' => '天象自然', 'key' => '洪水、大水', 'omen' => '平', 'desc' => '水主財，夢見清澈大水主進財；若水勢混濁猛烈沖毀房屋，則宜謹慎理財防破耗。'],
    ['cat' => '天象自然', 'key' => '地震、地動', 'omen' => '平', 'desc' => '主環境變動、工作調動或搬遷之象，宜順勢而為，不宜固執己見。'],
    ['cat' => '天象自然', 'key' => '火災、大火', 'omen' => '吉', 'desc' => '夢見熊熊大火燃燒旺盛，反主事業紅火、財運上升；若見濃煙不見火，則防口舌。'],
    ['cat' => '天象自然', 'key' => '下雪、積雪', 'omen' => '吉', 'desc' => '瑞雪兆豐年，主近期所求之事漸入佳境，但行事需耐得住性子。'],
    ['cat' => '天象自然', 'key' => '彩虹', 'omen' => '大吉', 'desc' => '雨過天晴之兆，主困頓將解、喜事臨門，遠行或合作皆宜。'],

    // 動物飛禽
    ['cat' => '動物飛禽', 'key' => '蛇、大蛇', 'omen' => '吉', 'desc' => '夢見蛇多主財運或潛在機會，被蛇咬主得意外之財；若被蛇纏身則防小人糾纏。'],
    ['cat' => '動物飛禽', 'key' => '龍、飛龍', 'omen' => '大吉', 'desc' => '夢見龍騰飛天或入懷，主大富大貴、升遷得志，乃極吉之兆。'],
    ['cat' => '動物飛禽', 'key' => '狗、犬吠', 'omen' => '平', 'desc' => '狗象徵忠誠與朋友，夢見溫馴之狗主得友助；被狗追咬則防親友誤會或口角。'],
    ['cat' => '動物飛禽', 'key' => '貓', 'omen' => '平', 'desc' => '夢見可愛之貓主生活安逸、桃花運佳；若被貓抓傷，宜防身邊暗算或破財。'],
    ['cat' => '動物飛禽', 'key' => '魚、捕魚', 'omen' => '大吉', 'desc' => '魚諧音「餘」，夢見活魚游水或捕魚有獲，主財源廣進、年年有餘。'],
    ['cat' => '動物飛禽', 'key' => '龜、烏龜', 'omen' => '大吉', 'desc' => '神龜主長壽與穩健，夢見烏龜入宅或爬行，主健康平安、事業根基穩固。'],
    ['cat' => '動物飛禽', 'key' => '蜘蛛', 'omen' => '吉', 'desc' => '蜘蛛又稱「喜子」，夢見蜘蛛結網主有遠方喜訊或意外之財上門。'],
    ['cat' => '動物飛禽', 'key' => '老虎', 'omen' => '吉', 'desc' => '夢見猛虎下山或騎虎，主權勢提升、事業突破；若被虎追趕則主壓力較大。'],

    // 人物身體
    ['cat' => '人物身體', 'key' => '掉牙、牙齒脫落', 'omen' => '平', 'desc' => '傳統解夢主家中長輩健康需多關懷，或代表近期面臨生活轉變與壓力釋放。'],
    ['cat' => '人物身體', 'key' => '懷孕、生子', 'omen' => '大吉', 'desc' => '主新計畫孕育成功、願望實現或家中添丁進財，乃生機勃勃之象。'],
    ['cat' => '人物身體', 'key' => '已故親人、亡者', 'omen' => '平', 'desc' => '多因思念所致；若亡者在夢中神情安詳交談，主有庇佑提醒之意，宜抽空祭拜。'],
    ['cat' => '人物身體', 'key' => '流血、受傷', 'omen' => '吉', 'desc' => '夢見見血反主破財消災、見紅大吉，象徵阻礙化解、財運轉順。'],
    ['cat' => '人物身體', 'key' => '剪頭髮、理髮', 'omen' => '吉', 'desc' => '主煩惱絲斷、除舊布新，近期糾結之事將告一段落，迎來新氣象。'],
    ['cat' => '人物身體', 'key' => '結婚、婚禮', 'omen' => '平', 'desc' => '未婚者夢之主姻緣將近；已婚者夢之則象徵新合作締結或責任加重。'],
    ['cat' => '人物身體', 'key' => '哭泣、流淚', 'omen' => '吉', 'desc' => '夢中放聲大哭反主「哭笑不得」之喜，代表壓力宣洩後將有好事發生。'],

    // 生活器物
    ['cat' => '生活器物', 'key' => '撿錢、黃金、鈔票', 'omen' => '吉', 'desc' => '夢見拾獲金銀財寶，主近期偏財運佳或有額外收穫，但見好就收不宜貪心。'],
    ['cat' => '生活器物', 'key' => '棺材', 'omen' => '大吉', 'desc' => '棺材諧音「升官發財」，夢見棺材入宅或抬棺，主事業高升、財運亨通。'],
    ['cat' => '生活器物', 'key' => '搬家、新居', 'omen' => '吉', 'desc' => '主運勢轉換、事業拓展或人際圈更新，宜積極把握新機會。'],
    ['cat' => '生活器物', 'key' => '掉手機、遺失錢包', 'omen' => '凶', 'desc' => '反映近期焦慮或缺乏安全感，現實中出門宜留意隨身財物與重要文件。'],
    ['cat' => '生活器物', 'key' => '開車、車禍', 'omen' => '平', 'desc' => '夢見順暢開車主掌控力佳；若遇車禍驚醒，則提醒近期行車與決策宜放慢腳步。'],
    ['cat' => '生活器物', 'key' => '考試、遲到', 'omen' => '平', 'desc' => '反映近期面臨考驗或自我要求較高，只要按部就班準備即可順利過關。'],
    ['cat' => '生活器物', 'key' => '神明、寺廟拜拜', 'omen' => '大吉', 'desc' => '主心誠則靈、貴人扶持，所謀之事可得神明庇佑，逢凶化吉。'],

    // 飲食花木
    ['cat' => '飲食花木', 'key' => '吃飯、宴席', 'omen' => '吉', 'desc' => '夢見豐盛佳餚與親友共食，主衣食無憂、人際和諧，近期有聚會喜事。'],
    ['cat' => '飲食花木', 'key' => '水果、摘果實', 'omen' => '大吉', 'desc' => '果實纍纍主努力終有回報，事業收成、財利雙收，孕婦夢之亦吉。'],
    ['cat' => '飲食花木', 'key' => '開花、大樹', 'omen' => '大吉', 'desc' => '枯木逢春、花開富貴之象，主家運昌隆、感情開花結果。'],
    ['cat' => '飲食花木', 'key' => '喝酒、醉酒', 'omen' => '平', 'desc' => '小酌主歡慶喜悅；若夢中爛醉失態，則提醒近期言行宜節制，防酒後誤事。'],
];

$categories = ['全部', '天象自然', '動物飛禽', '人物身體', '生活器物', '飲食花木'];
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$selectedCat = isset($_GET['cat']) && in_array($_GET['cat'], $categories) ? $_GET['cat'] : '全部';

$matchedDreams = [];
foreach ($dreamData as $item) {
    $catMatch = ($selectedCat === '全部' || $item['cat'] === $selectedCat);
    $keyMatch = ($keyword === '' || mb_stripos($item['key'], $keyword) !== false || mb_stripos($item['desc'], $keyword) !== false);
    if ($catMatch && $keyMatch) {
        $matchedDreams[] = $item;
    }
}

$hotKeywords = ['蛇', '掉牙', '懷孕', '已故親人', '棺材', '撿錢', '水', '火', '狗', '魚', '考試', '車禍'];
?>
<p>
<form class="form-inline ml-auto" id="dream-form" method="get" action="dream.php">
    <label class="mr-2" for="cat">【夢境分類】</label>
    <select id="cat" name="cat" class="form-control mr-3 mb-2 mb-sm-0">
        <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $selectedCat === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
        <?php endforeach; ?>
    </select>

    <label class="mr-2" for="keyword">【夢境關鍵字】</label>
    <div class="input-group">
        <input type="text" id="keyword" name="q" class="form-control" placeholder="例如：蛇、掉牙、撿錢..." 
               value="<?php echo htmlspecialchars($keyword); ?>">
        <div class="input-group-append">
            <button type="button" id="submitBtn" class="btn btn-warning">進呈</button>
        </div>
    </div>
</form>
</p>

<p class="small text-muted">
    熱門夢境速查：
    <?php foreach ($hotKeywords as $hk): ?>
        <a href="dream.php?q=<?php echo urlencode($hk); ?>" class="badge badge-light border mr-1"><?php echo htmlspecialchars($hk); ?></a>
    <?php endforeach; ?>
    <?php if ($keyword !== '' || $selectedCat !== '全部'): ?>
        <a href="dream.php" class="badge badge-secondary ml-2">清除條件</a>
    <?php endif; ?>
</p>

<div id="result">
<?php
$count = count($matchedDreams);
if ($keyword !== '' || $selectedCat !== '全部') {
    $filterDesc = ($selectedCat !== '全部' ? "分類「{$selectedCat}」" : "") . ($keyword !== '' ? "關鍵字「" . htmlspecialchars($keyword) . "」" : "");
    echo "<p>【查詢條件：{$filterDesc}，共找到 {$count} 筆解夢解析】</p>";
} else {
    echo "<p>【目前顯示全部常見夢境解析，共 {$count} 筆，可輸入關鍵字或點選上方標籤篩選】</p>";
}

if ($count === 0) {
    echo "<h3>【查無符合「" . htmlspecialchars($keyword) . "」的夢境紀錄，建議改用較簡短的單字（如：水、火、魚、錢）重新檢索。】</h3>";
} else {
    $first = $matchedDreams[0];
    if ($keyword !== '') {
        echo "<h3>【首筆解析 — 夢見{$first['key']}（{$first['omen']}）：{$first['desc']}】</h3>";
    }
}
?>
</div>

<?php if ($count > 0): ?>
<hr>
<h4>【周公解夢解析一覽表】</h4>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width: 14%;">分類</th>
                <th style="width: 22%;">夢境關鍵字</th>
                <th style="width: 10%;">吉凶</th>
                <th>傳統解夢解析</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchedDreams as $row): ?>
                <?php
                    $badgeClass = 'badge-secondary';
                    if ($row['omen'] === '大吉') $badgeClass = 'badge-danger';
                    elseif ($row['omen'] === '吉') $badgeClass = 'badge-warning';
                    elseif ($row['omen'] === '凶') $badgeClass = 'badge-dark';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['cat']); ?></td>
                    <td class="font-weight-bold"><?php echo htmlspecialchars($row['key']); ?></td>
                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($row['omen']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['desc']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div></div></div>
<?php include 'footer.php'; ?>
<script>
    var catSelect = document.getElementById('cat');
    var keywordInput = document.getElementById('keyword');
    var resultDiv = document.getElementById('result');

    function notifyChange() {
        resultDiv.innerHTML = '<p>【條件已改變，請點擊進呈查看新結果。】</p>';
    }

    catSelect.addEventListener('change', notifyChange);
    keywordInput.addEventListener('input', notifyChange);

    function doSearch() {
        var c = encodeURIComponent(catSelect.value);
        var q = encodeURIComponent(keywordInput.value.trim());
        var url = 'dream.php?cat=' + c;
        if (q !== '') {
            url += '&q=' + q;
        }
        window.location.href = url;
    }

    document.getElementById('submitBtn').addEventListener('click', doSearch);
    document.getElementById('dream-form').addEventListener('submit', function(e) {
        e.preventDefault();
        doSearch();
    });
</script>
</body>
</html>
