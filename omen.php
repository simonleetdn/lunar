<?php include 'metaseo.php'; ?>
<meta name="description" content="傳統民俗眼跳、耳鳴、噴嚏、心驚吉凶查詢，依照十二時辰與左右部位精準解析吉凶徵兆，提供最即時的農民曆占卜參考。">
<link rel="canonical" href="https://lunar.ioi.tw/omen.php" />
<title>眼跳、耳鳴、噴嚏、心驚吉凶查詢<?php echo isset($_GET['type']) && isset($_GET['time']) ? "：".htmlspecialchars($_GET['time']).htmlspecialchars($_GET['type']) : ''; ?></title>
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
		  <h2>眼跳、耳鳴、噴嚏、心驚吉凶查詢</h2>
<?php
date_default_timezone_set('Asia/Taipei');

function getChineseTime($hour) {
    if ($hour >= 23 || $hour < 1) return '子時';
    if ($hour >= 1 && $hour < 3) return '丑時';
    if ($hour >= 3 && $hour < 5) return '寅時';
    if ($hour >= 5 && $hour < 7) return '卯時';
    if ($hour >= 7 && $hour < 9) return '辰時';
    if ($hour >= 9 && $hour < 11) return '巳時';
    if ($hour >= 11 && $hour < 13) return '午時';
    if ($hour >= 13 && $hour < 15) return '未時';
    if ($hour >= 15 && $hour < 17) return '申時';
    if ($hour >= 17 && $hour < 19) return '酉時';
    if ($hour >= 19 && $hour < 21) return '戌時';
    if ($hour >= 21 && $hour < 23) return '亥時';
    return '子時';
}

$timeLabels = [
    '子時' => '子時 (23:00-00:59)',
    '丑時' => '丑時 (01:00-02:59)',
    '寅時' => '寅時 (03:00-04:59)',
    '卯時' => '卯時 (05:00-06:59)',
    '辰時' => '辰時 (07:00-08:59)',
    '巳時' => '巳時 (09:00-10:59)',
    '午時' => '午時 (11:00-12:59)',
    '未時' => '未時 (13:00-14:59)',
    '申時' => '申時 (15:00-16:59)',
    '酉時' => '酉時 (17:00-18:59)',
    '戌時' => '戌時 (19:00-20:59)',
    '亥時' => '亥時 (21:00-22:59)',
];

$types = ['眼跳', '耳鳴', '噴嚏', '心驚'];
$sides = ['左', '右'];

$currentHour = (int)date('G');
$defaultTime = getChineseTime($currentHour);

$selectedType = isset($_GET['type']) && in_array($_GET['type'], $types) ? $_GET['type'] : '眼跳';
$selectedSide = isset($_GET['side']) && in_array($_GET['side'], $sides) ? $_GET['side'] : '左';
$selectedTime = isset($_GET['time']) && array_key_exists($_GET['time'], $timeLabels) ? $_GET['time'] : $defaultTime;

$omenData = [
    '眼跳' => [
        '子時' => ['左' => '主有貴人來，大吉。', '右' => '主有飲食宴會，吉。'],
        '丑時' => ['左' => '主有憂愁思慮之事。', '右' => '主有人思念你。'],
        '寅時' => ['左' => '主遠方客人來訪。', '右' => '主有喜慶之事來臨，吉。'],
        '卯時' => ['左' => '主有貴客相訪，大吉。', '右' => '主平安無事，吉。'],
        '辰時' => ['左' => '主遠方親友將至。', '右' => '主有損害破財之事，宜防。'],
        '巳時' => ['左' => '主有飲食酒宴之樂。', '右' => '主有凶惡之事，須謹慎。'],
        '午時' => ['左' => '主有飲食福祿，吉。', '右' => '主有凶事發生，宜小心。'],
        '未時' => ['左' => '主有吉昌順遂之事。', '右' => '主有小喜慶臨門。'],
        '申時' => ['左' => '主有損財之虞，注意看管財物。', '右' => '主有女思念，或女性貴人相助。'],
        '酉時' => ['左' => '主有遠客來訪，吉。', '右' => '主遠方親友將至，喜相逢。'],
        '戌時' => ['左' => '主有遠客到來。', '右' => '主有聚會歡宴之事。'],
        '亥時' => ['左' => '主有貴客臨門，大吉。', '右' => '主有官司是非之象，宜忍讓。'],
    ],
    '耳鳴' => [
        '子時' => ['左' => '主女思念，有桃花或親友惦記。', '右' => '主失財破耗，宜留意隨身物品。'],
        '丑時' => ['左' => '主有口舌爭執，宜少言。', '右' => '主有訴訟是非，宜和氣生財。'],
        '寅時' => ['左' => '主失財之兆，投資消費需謹慎。', '右' => '主心急焦躁，宜靜心緩行。'],
        '卯時' => ['左' => '主有飲食宴樂，吉。', '右' => '主有客至相訪。'],
        '辰時' => ['左' => '主有遠行或出差之象。', '右' => '主有客至，宜熱情款待。'],
        '巳時' => ['左' => '主有凶事妨礙，行事宜低調。', '右' => '主大吉昌盛，諸事順遂。'],
        '午時' => ['左' => '主遠信將至，有好消息傳來。', '右' => '主有親朋好友來訪。'],
        '未時' => ['左' => '主有飲食之福。', '右' => '主遠人將至，久別重逢。'],
        '申時' => ['左' => '主有行人至，遠方來客。', '右' => '主有喜事臨門，大吉。'],
        '酉時' => ['左' => '主失財破費，不宜大額支出。', '右' => '主大吉大利，事事順心。'],
        '戌時' => ['左' => '主有飲食聚會，吉。', '右' => '主有客至，有事相商。'],
        '亥時' => ['左' => '主大吉昌盛，福星高照。', '右' => '主有飲食享樂之福。'],
    ],
    '噴嚏' => [
        '子時' => '主有飲食宴會，親友相聚。',
        '丑時' => '主有女人思念，或有客來求事。',
        '寅時' => '主有女相思，或有喜慶酒食。',
        '卯時' => '主有財喜臨門，或有人來問事。',
        '辰時' => '主終日有飲食之福，大吉。',
        '巳時' => '主有吉人來求財，合作順利。',
        '午時' => '主有貴客遠來，設宴款待，大吉。',
        '未時' => '主有飲食之樂，吉慶臨門。',
        '申時' => '主夜夢驚恐，飲食宜節制清淡。',
        '酉時' => '主有婦女來求事，宜耐心應對。',
        '戌時' => '主有女人思念，與人有和合之喜。',
        '亥時' => '主有虛驚一場，但終得貴人相助平安。',
    ],
    '心驚' => [
        '子時' => '主有女子思念，或有喜事臨門。',
        '丑時' => '主有惡事臨門，行事宜多加防範。',
        '寅時' => '主有客至，有飲食宴會之樂。',
        '卯時' => '主有飲食之福，及外人來訪。',
        '辰時' => '主有成合大喜之事，諸事順利。',
        '巳時' => '主有女思念，及喜事臨門。',
        '午時' => '主有飲食之樂，自然欣喜。',
        '未時' => '主有女人思念，有喜慶之兆。',
        '申時' => '主有大喜之事臨門，大吉。',
        '酉時' => '主有喜信將至，心願可成。',
        '戌時' => '主有貴人即至，百事大吉。',
        '亥時' => '主有喪服或遠方親友不佳之信，宜多關懷家人。',
    ],
];
?>
<p>
<form class="form-inline ml-auto" id="omen-form" method="get" action="omen.php">
    <label class="mr-2" for="type">【徵兆項目】</label>
    <select id="type" name="type" class="form-control mr-3 mb-2 mb-sm-0">
        <?php foreach ($types as $t): ?>
            <option value="<?php echo $t; ?>" <?php echo $selectedType === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
        <?php endforeach; ?>
    </select>

    <span id="side-wrapper" style="<?php echo in_array($selectedType, ['眼跳', '耳鳴']) ? '' : 'display:none;'; ?>">
        <label class="mr-2" for="side">【部位】</label>
        <select id="side" name="side" class="form-control mr-3 mb-2 mb-sm-0">
            <?php foreach ($sides as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $selectedSide === $s ? 'selected' : ''; ?>><?php echo $s; ?>邊</option>
            <?php endforeach; ?>
        </select>
    </span>

    <label class="mr-2" for="time">【發生時辰】</label>
    <div class="input-group">
        <select id="time" name="time" class="form-control">
            <?php foreach ($timeLabels as $k => $label): ?>
                <option value="<?php echo $k; ?>" <?php echo $selectedTime === $k ? 'selected' : ''; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
        <div class="input-group-append">
            <button type="button" id="submitBtn" class="btn btn-warning">進呈</button>
        </div>
    </div>
</form>
</p>

<div id="result">
<?php
if (in_array($selectedType, ['眼跳', '耳鳴'])) {
    $omenResult = $omenData[$selectedType][$selectedTime][$selectedSide] ?? '查無對應徵兆結果';
    $titleDesc = "{$selectedTime}（{$timeLabels[$selectedTime]}）{$selectedSide}{$selectedType}";
} else {
    $omenResult = $omenData[$selectedType][$selectedTime] ?? '查無對應徵兆結果';
    $titleDesc = "{$selectedTime}（{$timeLabels[$selectedTime]}）打{$selectedType}";
}
echo "<p>【當前查詢：{$titleDesc}】</p>";
echo "<h3>【吉凶解析：{$omenResult}】</h3>";
?>
</div>

<hr>
<h4>【<?php echo htmlspecialchars($selectedType); ?>十二時辰吉凶總表】</h4>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width: 24%;">時辰</th>
                <?php if (in_array($selectedType, ['眼跳', '耳鳴'])): ?>
                    <th style="width: 38%;">左<?php echo htmlspecialchars($selectedType); ?></th>
                    <th style="width: 38%;">右<?php echo htmlspecialchars($selectedType); ?></th>
                <?php else: ?>
                    <th>吉凶徵兆</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timeLabels as $k => $label): ?>
                <tr class="<?php echo $selectedTime === $k ? 'table-warning font-weight-bold' : ''; ?>">
                    <td><?php echo $label; ?></td>
                    <?php if (in_array($selectedType, ['眼跳', '耳鳴'])): ?>
                        <td><?php echo $omenData[$selectedType][$k]['左']; ?></td>
                        <td><?php echo $omenData[$selectedType][$k]['右']; ?></td>
                    <?php else: ?>
                        <td><?php echo $omenData[$selectedType][$k]; ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div></div></div>
<?php include 'footer.php'; ?>
<script>
    var typeSelect = document.getElementById('type');
    var sideWrapper = document.getElementById('side-wrapper');
    var sideSelect = document.getElementById('side');
    var timeSelect = document.getElementById('time');
    var resultDiv = document.getElementById('result');

    function toggleSideVisibility() {
        var val = typeSelect.value;
        if (val === '眼跳' || val === '耳鳴') {
            sideWrapper.style.display = 'inline';
        } else {
            sideWrapper.style.display = 'none';
        }
    }

    function notifyChange() {
        resultDiv.innerHTML = '<p>【條件已改變，請點擊進呈查看新結果。】</p>';
    }

    typeSelect.addEventListener('change', function() {
        toggleSideVisibility();
        notifyChange();
    });
    sideSelect.addEventListener('change', notifyChange);
    timeSelect.addEventListener('change', notifyChange);

    document.getElementById('submitBtn').addEventListener('click', function() {
        var t = encodeURIComponent(typeSelect.value);
        var s = encodeURIComponent(sideSelect.value);
        var tm = encodeURIComponent(timeSelect.value);
        var url = 'omen.php?type=' + t + '&time=' + tm;
        if (typeSelect.value === '眼跳' || typeSelect.value === '耳鳴') {
            url += '&side=' + s;
        }
        window.location.href = url;
    });
</script>
</body>
</html>
