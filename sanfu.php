<?php include 'metaseo.php'; ?>
<meta name="description" content="傳統農民曆三伏天、三九天與數九寒天自動推算工具，依據每年夏至、立秋與冬至節氣及天干庚日，精準計算初伏、中伏、末伏及一九至九九日期，提供三伏貼與三九貼養生參考。">
<link rel="canonical" href="https://lunar.ioi.tw/sanfu.php" />
<title>三伏天／三九天與數九寒天自動推算<?php echo isset($_GET['year']) ? "：".intval($_GET['year'])."年" : ''; ?></title>
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
		  <h2>三伏天／三九天與數九寒天自動推算</h2>
<?php
date_default_timezone_set('Asia/Taipei');
require 'Lunar.php';

use com\nlf\calendar\Lunar;

$currentYear = (int)date('Y');
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;
if ($selectedYear < 1901 || $selectedYear > 2100) {
    $selectedYear = $currentYear;
}
$todayStr = date('Y-m-d');
$weekDays = ['日', '一', '二', '三', '四', '五', '六'];

function formatDateWithWeek($dt, $weekDays) {
    $w = $weekDays[(int)$dt->format('w')];
    return $dt->format('Y-m-d') . " (週{$w})";
}

function calcSanfuAndShujiu($year, $weekDays) {
    $xiazhiDate = null;
    $liqiuDate = null;
    $dongzhiDate = null;

    // 1. 尋找該年夏至與立秋（6月15日 ~ 8月20日）
    $cursor = new DateTime("{$year}-06-15");
    for ($i = 0; $i < 70; $i++) {
        $lunar = Lunar::fromDate($cursor);
        $jq = $lunar->getJieQi();
        if ($jq === '夏至') {
            $xiazhiDate = clone $cursor;
        } elseif ($jq === '立秋') {
            $liqiuDate = clone $cursor;
            break;
        }
        $cursor->modify('+1 day');
    }

    // 2. 尋找該年冬至（12月15日 ~ 12月31日）
    $cursorW = new DateTime("{$year}-12-15");
    for ($i = 0; $i < 17; $i++) {
        $lunar = Lunar::fromDate($cursorW);
        if ($lunar->getJieQi() === '冬至') {
            $dongzhiDate = clone $cursorW;
            break;
        }
        $cursorW->modify('+1 day');
    }

    // 推算三伏天：夏至起第3庚為初伏，第4庚為中伏；立秋起第1庚為末伏
    $gengAfterXiazhi = [];
    if ($xiazhiDate) {
        $d = clone $xiazhiDate;
        for ($i = 0; $i < 45; $i++) {
            $l = Lunar::fromDate($d);
            if ($l->getDayGan() === '庚') {
                $gengAfterXiazhi[] = [
                    'date' => clone $d,
                    'ganzhi' => $l->getDayInGanZhi()
                ];
                if (count($gengAfterXiazhi) >= 4) break;
            }
            $d->modify('+1 day');
        }
    }

    $firstGengAfterLiqiu = null;
    if ($liqiuDate) {
        $d = clone $liqiuDate;
        for ($i = 0; $i < 15; $i++) {
            $l = Lunar::fromDate($d);
            if ($l->getDayGan() === '庚') {
                $firstGengAfterLiqiu = [
                    'date' => clone $d,
                    'ganzhi' => $l->getDayInGanZhi()
                ];
                break;
            }
            $d->modify('+1 day');
        }
    }

    $sanfuList = [];
    $totalSanfuDays = 30;
    if (count($gengAfterXiazhi) >= 4 && $firstGengAfterLiqiu) {
        $chuStart = $gengAfterXiazhi[2]['date'];
        $chuEnd = (clone $chuStart)->modify('+9 days');

        $zhongStart = $gengAfterXiazhi[3]['date'];
        $moStart = $firstGengAfterLiqiu['date'];
        $zhongEnd = (clone $moStart)->modify('-1 day');
        $zhongDays = (int)$zhongStart->diff($moStart)->days;

        $moEnd = (clone $moStart)->modify('+9 days');
        $totalSanfuDays = 10 + $zhongDays + 10;

        $sanfuList = [
            [
                'name' => '初伏',
                'rule' => '夏至後第 3 個庚日（' . $gengAfterXiazhi[2]['ganzhi'] . '日）',
                'start' => $chuStart,
                'end' => $chuEnd,
                'days' => 10,
                'note' => '三伏貼第一貼時機，宜避生冷、防中暑。'
            ],
            [
                'name' => '中伏',
                'rule' => '夏至後第 4 個庚日（' . $gengAfterXiazhi[3]['ganzhi'] . '日）',
                'start' => $zhongStart,
                'end' => $zhongEnd,
                'days' => $zhongDays,
                'note' => ($zhongDays === 20 ? '本年為「閏中伏」（20天），盛夏炎熱期較長。' : '三伏貼第二貼時機，注意補充水分與津液。')
            ],
            [
                'name' => '末伏',
                'rule' => '立秋後第 1 個庚日（' . $firstGengAfterLiqiu['ganzhi'] . '日）',
                'start' => $moStart,
                'end' => $moEnd,
                'days' => 10,
                'note' => '三伏貼加強貼時機，秋老虎餘熱仍盛。'
            ],
        ];
    }

    // 推算數九寒天（冬至起算，每九天為一「九」，共九九八十一天）
    $shujiuLabels = ['一九', '二九', '三九', '四九', '五九', '六九', '七九', '八九', '九九'];
    $shujiuNotes = [
        '冬至交九，寒氣漸生，宜早睡晚起、溫補腎陽。',
        '小寒前後，天氣愈冷，注意頭頸與足部保暖。',
        '【三九天】全年最寒冷時段，亦是「三九貼」穴位敷貼黃金期。',
        '大寒前後，冰封大地，宜適度運動促進氣血循環。',
        '立春前後，陽氣初動，俗諺「五九六九，沿河看柳」。',
        '雨水前後，冰雪漸融，乍暖還寒仍須防風邪。',
        '驚蟄前後，河開雁來，萬物萌發。',
        '春分前後，春意漸濃，宜疏肝理氣、少酸增甘。',
        '出九結束，春耕開始，寒冬正式告一段落。',
    ];

    $shujiuList = [];
    $firstRenAfterDongzhi = null;
    if ($dongzhiDate) {
        // 尋找冬至後第一個壬日（民間「逢壬數九」算法參考）
        $dRen = clone $dongzhiDate;
        for ($i = 0; $i < 15; $i++) {
            $lRen = Lunar::fromDate($dRen);
            if ($lRen->getDayGan() === '壬') {
                $firstRenAfterDongzhi = [
                    'date' => clone $dRen,
                    'ganzhi' => $lRen->getDayInGanZhi()
                ];
                break;
            }
            $dRen->modify('+1 day');
        }

        for ($idx = 0; $idx < 9; $idx++) {
            $sDate = (clone $dongzhiDate)->modify('+' . ($idx * 9) . ' days');
            $eDate = (clone $sDate)->modify('+8 days');
            $shujiuList[] = [
                'name' => $shujiuLabels[$idx],
                'start' => $sDate,
                'end' => $eDate,
                'is_sanjiu' => ($idx === 2),
                'note' => $shujiuNotes[$idx]
            ];
        }
    }

    return [
        'xiazhi' => $xiazhiDate,
        'liqiu' => $liqiuDate,
        'dongzhi' => $dongzhiDate,
        'sanfu' => $sanfuList,
        'totalSanfuDays' => $totalSanfuDays,
        'shujiu' => $shujiuList,
        'firstRen' => $firstRenAfterDongzhi
    ];
}

$calc = calcSanfuAndShujiu($selectedYear, $weekDays);

// 判斷今天是否正處於該年三伏或數九期間
$todayStatus = '';
foreach ($calc['sanfu'] as $sf) {
    if ($todayStr >= $sf['start']->format('Y-m-d') && $todayStr <= $sf['end']->format('Y-m-d')) {
        $dayNum = (int)$sf['start']->diff(new DateTime($todayStr))->days + 1;
        $todayStatus = "今日（{$todayStr}）正值【{$sf['name']}第 {$dayNum} 天】！";
    }
}
if ($todayStatus === '') {
    foreach ($calc['shujiu'] as $sj) {
        if ($todayStr >= $sj['start']->format('Y-m-d') && $todayStr <= $sj['end']->format('Y-m-d')) {
            $dayNum = (int)$sj['start']->diff(new DateTime($todayStr))->days + 1;
            $todayStatus = "今日（{$todayStr}）正值【數九寒天・{$sj['name']}第 {$dayNum} 天】！";
        }
    }
}
?>
<p>
<form class="form-inline ml-auto" id="sanfu-form" method="get" action="sanfu.php">
    <label class="mr-2" for="yearInput">【查詢西元年分】</label>
    <div class="input-group">
        <input type="number" id="yearInput" name="year" class="form-control" min="1901" max="2100" 
               value="<?php echo $selectedYear; ?>">
        <div class="input-group-append">
            <button type="button" id="submitBtn" class="btn btn-warning">進呈</button>
        </div>
    </div>
    <div class="ml-sm-3 mt-2 mt-sm-0">
        <a href="sanfu.php?year=<?php echo $selectedYear - 1; ?>" class="btn btn-outline-secondary btn-sm">&laquo; <?php echo $selectedYear - 1; ?>年</a>
        <a href="sanfu.php?year=<?php echo $currentYear; ?>" class="btn btn-outline-danger btn-sm mx-1">今年 (<?php echo $currentYear; ?>)</a>
        <a href="sanfu.php?year=<?php echo $selectedYear + 1; ?>" class="btn btn-outline-secondary btn-sm"><?php echo $selectedYear + 1; ?>年 &raquo;</a>
    </div>
</form>
</p>

<div id="result">
<?php
if ($calc['xiazhi'] && $calc['liqiu'] && $calc['dongzhi']) {
    $xzStr = formatDateWithWeek($calc['xiazhi'], $weekDays);
    $lqStr = formatDateWithWeek($calc['liqiu'], $weekDays);
    $dzStr = formatDateWithWeek($calc['dongzhi'], $weekDays);
    $sanjiuItem = $calc['shujiu'][2];
    $sjRange = $sanjiuItem['start']->format('Y-m-d') . ' ～ ' . $sanjiuItem['end']->format('Y-m-d');

    echo "<p>【西元 {$selectedYear} 年關鍵節氣：夏至 {$xzStr}、立秋 {$lqStr}、冬至 {$dzStr}】</p>";
    if ($todayStatus !== '') {
        echo "<div class='alert alert-warning font-weight-bold'>{$todayStatus}</div>";
    }
    echo "<h3>【{$selectedYear} 年三伏天共 {$calc['totalSanfuDays']} 天；三九天為 {$sjRange}】</h3>";
} else {
    echo "<h3>【查無 {$selectedYear} 年節氣資料，請確認年份範圍（1901～2100）。】</h3>";
}
?>
</div>

<?php if (!empty($calc['sanfu'])): ?>
<hr>
<h4>【<?php echo $selectedYear; ?> 年三伏天一覽表（夏至三庚入伏）】</h4>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width: 10%;">伏期</th>
                <th style="width: 24%;">起訖日期</th>
                <th style="width: 10%;">天數</th>
                <th style="width: 24%;">推算依據</th>
                <th>養生與三伏貼提示</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calc['sanfu'] as $sf): ?>
                <?php
                    $sStr = $sf['start']->format('Y-m-d');
                    $eStr = $sf['end']->format('Y-m-d');
                    $isCurrent = ($todayStr >= $sStr && $todayStr <= $eStr);
                ?>
                <tr class="<?php echo $isCurrent ? 'table-warning font-weight-bold' : ''; ?>">
                    <td><?php echo $sf['name']; ?></td>
                    <td><?php echo "{$sStr} ～ {$eStr}"; ?></td>
                    <td><?php echo $sf['days']; ?> 天</td>
                    <td><?php echo $sf['rule']; ?></td>
                    <td><?php echo $sf['note']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($calc['shujiu'])): ?>
<hr>
<h4>【<?php echo $selectedYear; ?>～<?php echo $selectedYear + 1; ?> 年數九寒天一覽表（冬至交九）</h4>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-light">
            <tr>
                <th style="width: 12%;">九序</th>
                <th style="width: 26%;">起訖日期</th>
                <th style="width: 10%;">天數</th>
                <th>物候與三九養生提示</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calc['shujiu'] as $sj): ?>
                <?php
                    $sStr = $sj['start']->format('Y-m-d');
                    $eStr = $sj['end']->format('Y-m-d');
                    $isCurrent = ($todayStr >= $sStr && $todayStr <= $eStr);
                    $rowClass = '';
                    if ($isCurrent) {
                        $rowClass = 'table-warning font-weight-bold';
                    } elseif ($sj['is_sanjiu']) {
                        $rowClass = 'table-info font-weight-bold';
                    }
                ?>
                <tr class="<?php echo $rowClass; ?>">
                    <td><?php echo $sj['name']; ?><?php echo $sj['is_sanjiu'] ? ' <span class="badge badge-danger">最冷</span>' : ''; ?></td>
                    <td><?php echo "{$sStr} ～ {$eStr}"; ?></td>
                    <td>9 天</td>
                    <td><?php echo $sj['note']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if ($calc['firstRen']): ?>
<p class="small text-muted">
    註：傳統通書另有「逢壬數九」算法（從冬至後第一個壬日起算），<?php echo $selectedYear; ?> 年冬至後首個壬日為 <strong><?php echo $calc['firstRen']['date']->format('Y-m-d'); ?>（<?php echo $calc['firstRen']['ganzhi']; ?>日）</strong>。現代中醫「三九貼」與一般曆書皆以<strong>冬至當日</strong>作為一九第一天。
</p>
<?php endif; ?>
<?php endif; ?>

</div></div></div>
<?php include 'footer.php'; ?>
<script>
    var yearInput = document.getElementById('yearInput');
    var resultDiv = document.getElementById('result');

    yearInput.addEventListener('input', function() {
        resultDiv.innerHTML = '<p>【年分已改變，請點擊進呈查看新結果。】</p>';
    });

    function doCalc() {
        var y = parseInt(yearInput.value, 10);
        if (isNaN(y) || y < 1901 || y > 2100) {
            alert('請輸入 1901 至 2100 之間的西元年分');
            return;
        }
        window.location.href = 'sanfu.php?year=' + y;
    }

    document.getElementById('submitBtn').addEventListener('click', doCalc);
    document.getElementById('sanfu-form').addEventListener('submit', function(e) {
        e.preventDefault();
        doCalc();
    });
</script>
</body>
</html>
