<?php include 'metaseo.php'; ?>
  <title>農民曆稱骨算命：生於<?php echo isset($_GET['selectedDateTime']) ? htmlspecialchars($_GET['selectedDateTime']) : ''; ?></title>
<?php include 'head.php'; ?>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-danger">
	  <div class="container-md">
    <a class="navbar-brand" href="lunardate.php">農民曆（黃曆）</a>
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
      </ul>
    </div>
	</div>	  
  </nav>
	<div class="container">
    <div class="row">
      <div class="col-md-12">
		  <h2>袁天罡稱骨算命</h2><hr/>
<form class="form-inline ml-auto" id="daytime-form" method="get">
    <label class="mr-2" for="daytime">【陽曆出生日期時間】</label>
    <input type="datetime-local" id="daytime" name="selectedDateTime" class="form-control mr-2" 
           value="<?php echo isset($_GET['selectedDateTime']) ? htmlspecialchars($_GET['selectedDateTime']) : ''; ?>" />
</form><hr/>

<script>
    document.getElementById('daytime').addEventListener('change', function() {
        var selectedDateTime = this.value;
        window.location.href = 'yuangang.php?selectedDateTime=' + selectedDateTime;
    });
</script>

		  
<?php
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;		  
		  
function getWeightByYear($year) {
    // 根據輸入找到對應的值
	$yearvalues = [
    '甲子' => 12, '丙子' => 16, '戊子' => 15, '庚子' => 7, '壬子' => 5,
    '乙丑' => 9, '丁丑' => 8, '己丑' => 7, '辛丑' => 7, '癸丑' => 7,
    '丙寅' => 6, '戊寅' => 8, '庚寅' => 9, '壬寅' => 9, '甲寅' => 12,
    '丁卯' => 7, '己卯' => 19, '辛卯' => 12, '癸卯' => 12, '乙卯' => 8,
    '戊辰' => 12, '庚辰' => 12, '壬辰' => 10, '甲辰' => 8, '丙辰' => 8,
    '己已' => 5, '辛巳' => 6, '癸已' => 7, '乙已' => 7, '丁已' => 6,
    '庚午' => 9, '壬午' => 8, '甲午' => 15, '丙午' => 13, '戊午' => 19,
    '辛未' => 8, '癸未' => 7, '乙未' => 6, '丁未' => 5, '己未' => 6,
    '壬申' => 7, '甲申' => 5, '丙申' => 5, '戊申' => 14, '庚申' => 8,
    '癸酉' => 8, '乙酉' => 15, '丁酉' => 14, '己酉' => 5, '辛酉' => 16,
    '甲戌' => 15, '丙戌' => 6, '戊戌' => 14, '庚戌' => 9, '壬戌' => 10,
    '乙亥' => 9, '丁亥' => 16, '己亥' => 9, '辛亥' => 17, '癸亥' => 7,
];


    if (array_key_exists($year, $yearvalues)) {
        $value = $yearvalues[$year];
        return $value;
    } else {
        return "找不到對應的值。";
    }
}

function getWeightByMonth($month) {
    // 定義月份和對應的重量
$weights = [
    '正' => '6', '二' => '7', '三' => '18', '四' => '9',
    '五' => '5', '六' => '16', '七' => '9', '八' => '15',
    '九' => '18', '十' => '8', '冬' => '9', '臘' => '5',
];

    if (array_key_exists($month, $weights)) {
        $value = $weights[$month];
        return $value;
    } else {
        return "找不到{$month}對應的重量。";
    }
}

function getWeightByDay($day) {
    // 定義出生日和對應的重量
    $weights = [
    '初一' => '5', '初二' => '16', '初三' => '8', '初四' => '15', '初五' => '16',
    '初六' => '15', '初七' => '8', '初八' => '16', '初九' => '8', '初十' => '16',
    '十一' => '9', '十二' => '17', '十三' => '8', '十四' => '17', '十五' => '16',
    '十六' => '8', '十七' => '9', '十八' => '18', '十九' => '5', '二十' => '15',
    '廿一' => '16', '廿二' => '9', '廿三' => '8', '廿四' => '9', '廿五' => '15',
    '廿六' => '18', '廿七' => '7', '廿八' => '8', '廿九' => '16', '三十' => '6',
];


    if (array_key_exists($day, $weights)) {
        $value = $weights[$day];
        return $value;
    } else {
        return "找不到{$day}對應的重量。";
    }
}

function getWeightByTime($time) {
    // 定義出生時辰和對應的重量
    $weights = [
    '子時' => '16', '丑時' => '6', '寅時' => '7', '卯時' => '16', '辰時' => '9',
    '巳時' => '16', '午時' => '18', '未時' => '8', '申時' => '8', '酉時' => '9',
    '戌時' => '6', '亥時' => '6',
];

    if (array_key_exists($time, $weights)) {
        $value = $weights[$time];
        return $value;
    } else {
        return "找不到{$time}對應的重量。";
    }
}

$destinies = [
    22 => '身寒骨冷苦伶仃，此命推來生乞人。碌碌巴巴無度日，終年打拱過平年。',
    23 => '此命推來骨自輕，求謀作事事難成。妻兒兄弟應難許，別處他鄉作散人。',
    24 => '此命推來福祿無，門庭困苦總難營。六親骨肉皆無靠，流到他鄉作老翁。',
    25 => '此命推來祖業微，門庭營度似稀奇。六親骨肉如冰炭，一生勤勞自把持。',
    26 => '平生衣祿苦中求，獨自經營事不休。離祖出門宜早計，晚來衣祿自無憂。',
    27 => '一生作事少商量，難靠祖宗作主張。獨馬單槍空做去，早來晚歲部無長。',
    28 => '一生作事似飄蓬，祖宗產業在夢中。若不過房並改姓，也當移徙兩三通。',
    29 => '初年運限未曾享，縱有功名在後底。須過四旬繞可上，移居改姓始為良。',
    30 => '勞勞碌碌苦中求，東走西奔何日休。若使終身勤與儉，老來稍可免憂愁。',
    31 => '忙忙碌碌苦中求，何日雲開見日頭。難得祖基家可立，中年衣食漸無憂。',
    32 => '初年運蹇事難謀，漸有財源如水流。到得中年衣食旺，那時名利一齊來。',
    33 => '早年做事事難成，百計徒勞枉費心。半世自如流水去，後來運到得黃金。',
    34 => '此命福氣果如何，僧道門中衣祿多。離祖出家方得妙，終朝拜佛念彌陀。',
    35 => '平生福量不周全，祖業根基覺少傳。營業生涯宜守舊，時來衣食勝從前。',
    36 => '不須勞碌過平生，獨自成家福不輕。早有福星常照命，任君行去百般成。',
    37 => '此命般般事不成，弟兄少力自孤成。雖然祖業須微有，來得明時去得明。',
    38 => '一生骨肉最清高，早入黃門姓名標。待看看將三十六，藍袍脫去換紅袍。',
    39 => '此命終身運不窮，勞勞作事盡皆空。苦心竭力成家計，到得那時在夢中。',
    40 => '生平衣祿是綿長，件件心中自主張。前面風霜多受過，後來必定享安康。',
    41 => '此命推來事不同，為人能幹略凡庸。中年還有逍遙福，不比前年運未通。',
    42 => '得寬懷處且寬懷，何用雙眉皺不開。若使中年命運濟，那時名利一齊來。',
    43 => '為人心性最聰明，作事軒昂近貴人。衣祿一生天數定，不須勞碌是豐享。',
    44 => '來事由天莫苦求，須知福祿勝前途。當年財帛難如意，晚景欣然便不憂。',
    45 => '名利推來竟若何，前途辛苦後奔波。命中難養男與女，骨肉扶持也不多。',
    46 => '東西南北盡皆空，出姓移名更覺隆。衣祿無虧天數定，中年晚景一般同。',
    47 => '此命推來旺末年，妻榮子貴自怡然。平生原有滔滔福，可有財源如水源。',
    48 => '幼年運道未曾享，若是蹉跎再不興。兄弟六親皆無靠，一身事業晚年成。',
    49 => '此命推來福不輕，自成自立耀門庭。從來富貴人親近，使婢差奴過一生。',
    50 => '為名為利終日勞，中年福祿也多遭。老來是有財星照，不比前番日下高。',
    51 => '一世榮華事事通，不須勞碌自享豐。弟兄叔侄皆如意，家業成時福祿宏。',
    52 => '一世享通事事能，不須勞思自然能。家族欣然心皆好，家業豐享自稱心。',
    53 => '此格推來氣像真，興家發達在其中。一生福祿安排家，欲是人間一富翁。',
    54 => '此命推來厚且清，詩畫滿腹看功成。豐衣足食自然穩，正是人間有福人。',
    55 => '走馬揚鞭爭名利，少年做事費籌謀。一朝福祿源源至，富貴榮華耀六親。',
    56 => '此格推來禮義通，一生福祿用無窮。甜酸苦辣皆嘗過，財源滾滾穩且豐。',
    57 => '福祿豐盈萬事全，一生榮耀顯雙親。名揚威振人欽敬，處世逍遙似遇春。',
    58 => '平生福祿自然來，名利雙全福祿偕。雁塔題名為貴客，紫袍玉帶走金階。',
    59 => '細推此格妙且清，必定財高禮義通。甲第之中應有分，揚鞭走馬顯威榮。',
    60 => '一朝金榜快題名，顯祖榮宗立大功。衣食定然原裕足，田園財帛更豐盛。',
    61 => '此命推來事不同，為人能幹略凡庸。中年還有逍遙福，不比前年運未通。',
    62 => '得寬懷處且寬懷，何用雙眉皺不開。若使中年命運濟，那時名利一齊來。',
    63 => '命主為官福祿長，得來富貴定非常。名題雁塔傳金榜，定中高科天下揚。',
    64 => '此格威權不可擋，紫袍金帶坐高望。榮華富貴雖能及，積玉堆金滿儲倉。',
    65 => '細推此命福不輕，安國安邦極品人。文紛雕梁徽富貴，威聲照耀四方聞。',
    66 => '此格人間一福人，堆金積玉滿堂春。從來富貴由天定，正勿垂紳謁聖君。',
    67 => '此命生來福自宏，田園家業最高隆。平生衣祿豐盈足，一世榮華萬事通。',
    68 => '富貴由大莫苦求，萬金家計不須謀。十年不比前番事，祖業根基水上舟。',
    69 => '君是人間前祿星，一生富貴眾人欽。縱然福祿由天定，安享榮華過一生。',
    70 => '此命推來福不輕，不須愁慮苦勞心。一生天定衣與祿，富貴榮華主一生。',
    71 => '此命生來大不同，公侯卿相在其中。一生自有逍遙福，富貴榮華極品隆。',
];

function convertToChineseWeight($weight) {
    $chineseNums = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    $jin = intdiv($weight, 10);
    $liang = $weight % 10;
    
    if ($jin == 0) {
        return $chineseNums[$liang] . '錢';
    } else {
        return $chineseNums[$jin] . '兩' . $chineseNums[$liang] . '錢';
    }
}

if (isset($_GET['selectedDateTime'])) {
    $selectedDateTime = $_GET['selectedDateTime'];
    $dateTime = new DateTime($selectedDateTime);
    $year = $dateTime->format('Y');
    $month = $dateTime->format('m');
    $day = $dateTime->format('d');
    $hour = $dateTime->format('H');
    
    // 將公曆日期轉換為農曆
    $gregorianDate = new DateTime("$year-$month-$day");
    $lunar = Lunar::fromDate($gregorianDate);
    $yearInGanZhi = $lunar->getYearInGanZhi();
	$yearShengXiao = $lunar->getYearShengXiao();
    $monthInChinese = $lunar->getMonthInChinese();
    $dayInChinese = $lunar->getDayInChinese();
    $timeInChinese = getChineseTime($hour);

    $resultyear = getWeightByYear($yearInGanZhi);
    $resultmonth = getWeightByMonth($monthInChinese);
    $resultday = getWeightByDay($dayInChinese);
    $resulttime = getWeightByTime($timeInChinese);

    $resultyearChinese = convertToChineseWeight($resultyear);
    $resultmonthChinese = convertToChineseWeight($resultmonth);
    $resultdayChinese = convertToChineseWeight($resultday);
    $resulttimeChinese = convertToChineseWeight($resulttime);
    $destweight = $resultyear + $resultmonth + $resultday + $resulttime;
    $destweightChinese = convertToChineseWeight($destweight);	
    
    echo "<span class='mt-3'>【農曆{$yearInGanZhi}{$yearShengXiao}年{$monthInChinese}月{$dayInChinese}日{$timeInChinese}生，";
    echo "此命重量合計{$destweightChinese}。其中，";
    echo "{$yearInGanZhi}年重{$resultyearChinese}，";
    echo "{$monthInChinese}月重{$resultmonthChinese}，";
    echo "{$dayInChinese}日重{$resultdayChinese}，";
    echo "{$timeInChinese}重{$resulttimeChinese}。】</span><hr/>";
    $result = $destinies[$destweight] ?? '查無對應命理結果';
    echo "<h3 class='mt-4'>【".$result."】</h3>";
}

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
    return '';
}
?>
</div></div></div>
<?php include 'footer.php'; ?>		  
</body>	
</html>
