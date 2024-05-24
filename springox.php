<?php include 'metaseo.php'; ?>
<meta name="description" content="探索不同年份的春牛圖及其象徵意義，了解每一年圖中描繪的豐收預兆和文化內涵，感受中國傳統藝術的魅力。">
<link rel="canonical" href="https://lunar.ioi.tw/springox.php" />
  <title>春牛圖</title>
<?php include 'head.php'; ?>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-danger">
	  <div class="container-md">
    <a class="navbar-brand" href="lunardate.php">農民曆</a>
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
<div class="container"><div class="row"><div class="col-md-12">
		  <h2>春牛圖</h2><hr/>
	    <form class="form-inline ml-auto" id="year-form" method="get">
        <label class="mr-2 text-light" for="year">選擇年份</label>
        <input type="number" id="year" name="year" value="<?php echo isset($_GET['year']) ? htmlspecialchars($_GET['year']) : date('Y'); ?>" class="form-control" style="width: 90px;" />
        <button type="submit" class="btn btn-primary">提交</button>
    </form>
	  
<?php
	
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

	
if (isset($_GET['year'])) {

         // 獲取使用者輸入的年份
        $year = (int)$_GET['year'];

        // 使用者輸入年份的2月4日（立春的常見日期）
        $gregorianDate = new DateTime("$year-04-04");
        $lunar = Lunar::fromDate($gregorianDate);

        // 獲取立春日期
        $jieQiTable = $lunar->getJieQiTable();
        $liChun = $jieQiTable['立春'];

        // 轉換立春日期為 DateTime 對象
        $liChunDateTime = new DateTime($liChun->toYmd());

        // 創建對應立春日期的Lunar對象
        $lunarLiChun = Lunar::fromDate($liChunDateTime);

        // 獲取立春的天干
        $liChunDayGan = $lunarLiChun->getDayGan();
	    // 獲取立春的地支
        $liChunDayZhi = $lunarLiChun->getDayZhi();
		// 獲取立春的納音
        $liChunDayNaYin = mb_substr($lunarLiChun->getDayNaYin(), 2, 1);
		// 獲取年天干
	    $yearGan = $lunar->getYearGan();
		// 獲取年地支
		$yearZhi = $lunar->getYearZhi();
	// 获取年纳音
$yearNaYin = $lunar->getYearNaYin();
	

// 定义牛头颜色与年干的对应关系
$headColors = [
    '甲' => '青色', '乙' => '青色',
    '丙' => '红色', '丁' => '红色',
    '戊' => '黄色', '己' => '黄色',
    '庚' => '白色', '辛' => '白色',
    '壬' => '黑色', '癸' => '黑色'
];

// 定义牛身颜色与年支的对应关系
$bodyColors = [
    '子' => '黑色', '亥' => '黑色',
    '寅' => '青色', '卯' => '青色',
    '巳' => '红色', '午' => '红色',
    '申' => '白色', '酉' => '白色',
    '丑' => '黄色', '辰' => '黄色',
    '未' => '黄色', '戌' => '黄色'
];

// 定义牛腹颜色与年纳音的对应关系
$bellyColors = [
    '金' => '白色',
    '木' => '青色',
    '水' => '黑色',
    '火' => '红色',
    '土' => '黄色'
];

	// 設定牛尾顏色的規則，立春日干
	$tailColors = [
    '甲' => '青色',
    '乙' => '青色',
    '丙' => '紅色',
    '丁' => '紅色',
    '戊' => '黃色',
    '己' => '黃色',
    '庚' => '白色',
    '辛' => '白色',
    '壬' => '黑色',
    '癸' => '黑色'
];
	
	// 設定牛脛顏色的規則，立春日支
	$footColors = [
    '子' => '黑色',
    '亥' => '黑色',
    '寅' => '青色',
    '卯' => '青色',
    '巳' => '紅色',
    '午' => '紅色',
    '申' => '白色',
    '酉' => '白色',
    '丑' => '黃色',
    '辰' => '黃色',
    '未' => '黃色',
    '戌' => '黃色'
];
	
	// 設定牛蹄顏色的規則，立春日納音
	$tiColors = [
    '金' => '白色',
    '木' => '青色',
    '水' => '黑色',
    '火' => '紅色',
    '土' => '黃色'
];
	
	// 牛繩顏色對應立春天干
$ropeColors = [
    '甲' => '白色',
    '乙' => '白色',
    '丙' => '黑色',
    '丁' => '黑色',
    '戊' => '青色',
    '己' => '青色',
    '庚' => '紅色',
    '辛' => '紅色',
    '壬' => '黃色',
    '癸' => '黃色'
];

// 牛繩質地對應立春地支
$ropeMaterials = [
    '子' => '苧繩',
    '午' => '苧繩',
    '酉' => '苧繩',
    '卯' => '苧繩',
    '丑' => '絲繩',
    '辰' => '絲繩',
    '未' => '絲繩',
    '戌' => '絲繩',
    '寅' => '麻繩',
    '巳' => '麻繩',
    '申' => '麻繩',
    '亥' => '麻繩'
];


        // 設定流年屬陽的天干
        $yangGan = ['甲', '丙', '戊', '庚', '壬'];

        // 判斷流年屬陰還是屬陽
        $isYangYear = in_array($liChunDayGan, $yangGan);

        // 根據流年屬陰陽來確定牛尾擺向、牛口開合和牛踏板用門的左或右扇
        $tailDirection = $isYangYear ? '左繳' : '右繳';
        $mouthState = $isYangYear ? '開' : '閉';
        $doorSide = $isYangYear ? '左扇' : '右扇';	

        // 設定芒神形象的規則
        $mangShenImage = [
            '子' => '少壯',
            '卯' => '少壯',
            '午' => '少壯',
            '酉' => '少壯',
            '丑' => '童子',
            '辰' => '童子',
            '未' => '童子',
            '戌' => '童子',
            '寅' => '老人',
            '巳' => '老人',
            '申' => '老人',
            '亥' => '老人'
        ];
	
	
		// 芒神衣服和腰帶顏色
        $mangShenClothes = [
            '子' => ['衣服' => '黃衣', '腰帶' => '青腰帶'],
            '亥' => ['衣服' => '黃衣', '腰帶' => '青腰帶'],
            '丑' => ['衣服' => '青衣', '腰帶' => '白腰帶'],
            '辰' => ['衣服' => '青衣', '腰帶' => '白腰帶'],
            '未' => ['衣服' => '青衣', '腰帶' => '白腰帶'],
            '戌' => ['衣服' => '青衣', '腰帶' => '白腰帶'],
            '寅' => ['衣服' => '白衣', '腰帶' => '紅腰帶'],
            '卯' => ['衣服' => '白衣', '腰帶' => '紅腰帶'],
            '巳' => ['衣服' => '黑衣', '腰帶' => '黃腰帶'],
            '午' => ['衣服' => '黑衣', '腰帶' => '黃腰帶'],
            '申' => ['衣服' => '紅衣', '腰帶' => '黑腰帶'],
            '酉' => ['衣服' => '紅衣', '腰帶' => '黑腰帶']
        ];

        // 芒神髮髻位置
        $mangShenHair = [
            '金' => ['髮髻' => '平梳兩髻', '位置' => '在耳前'],
            '木' => ['髮髻' => '平梳兩髻', '位置' => '在耳後'],
            '水' => ['髮髻' => '平梳兩髻', '位置' => '左在耳前，右在耳後'],
            '火' => ['髮髻' => '平梳兩髻', '位置' => '右在耳前，左在耳後'],
            '土' => ['髮髻' => '平梳兩髻', '位置' => '在頂真上']
        ];

        // 芒神耳朵蓋法和帽子
        $earCover = [
            '子' => '罨耳全戴',
            '丑' => '罨耳全戴',
            '寅' => '罨耳揭起左邊',
            '卯' => '罨耳用右手提',
            '辰' => '罨耳用左手提',
            '巳' => '罨耳用右手提',
            '午' => '罨耳用左手提',
            '未' => '罨耳用右手提',
            '申' => '罨耳用左手提',
            '酉' => '罨耳用右手提',
            '戌' => '罨耳用左手提',
            '亥' => '罨耳揭起右邊'
        ];

	// 定义芒神行纏鞋褲与纳音的对应关系
$shoePantsSettings = [
    '金' => '行纏鞋褲俱全，左行纏懸於左腰',
    '木' => '行纏鞋褲俱全，右行纏懸於右腰',
    '水' => '行纏鞋褲俱全',
    '火' => '行纏鞋褲俱無',
    '土' => '著褲，無行纏鞋子'
];

// 定义日支与鞭杖上丝带结的对应关系
$ribbonKnot = [
    '子' => '苧結', '卯' => '苧結', '午' => '苧結', '酉' => '苧結',
    '丑' => '絲結', '辰' => '絲結', '未' => '絲結', '戌' => '絲結',
    '寅' => '麻結', '巳' => '麻結', '申' => '麻結', '亥' => '麻結'
];	
// 获取立春日期的年月日时分秒
$liChunHour = $liChun->getHour();

// 计算立春时辰
$hourIndex = floor(($liChunHour + 1) / 2) % 12; // 每时辰两个小时，加1是为了从子时开始计算，然后取余12得到时辰索引
$hourZhi = ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'][$hourIndex];

// 获取春节（正月元旦）的日期
$springFestival = $jieQiTable['春節'];
$springFestivalDateTime = new DateTime($springFestival->toYmdHms());

// 计算立春距正月元旦的天数差
$interval = $liChunDateTime->diff($springFestivalDateTime);
$daysDifference = $interval->days;
$isBefore = $liChunDateTime < $springFestivalDateTime;

// 确定芒神的位置
if ($daysDifference <= 5) {
    $mangShenPosition = "芒神忙與牛並立";
} elseif ($isBefore) {
    $mangShenPosition = "芒神早忙，立於牛前邊";
} else {
    $mangShenPosition = "芒神晚閑，立於牛後";
}	
	
        // 輸出春牛圖的內容
        echo "【春牛身高四尺，長八尺，尾長一尺二寸，牛頭{$headColors[$yearGan]}，牛身{$bodyColors[$yearZhi]}，牛腹{$bellyColors[mb_substr($yearNaYin, 2, 1)]}，牛角、牛耳、牛尾{$tailColors[$liChunDayGan]}，牛脛{$footColors[$liChunDayZhi]}，牛蹄{$tiColors[$liChunDayNaYin]}，牛尾{$tailDirection}。牛口{$mouthState}，牛籠頭拘繩桑柘木，用{$ropeMaterials[$liChunDayZhi]}結{$ropeColors[$liChunDayGan]}，牛踏板縣門{$doorSide}。芒神身高三尺六寸五分，面如{$mangShenImage[$yearZhi]}像，{$mangShenClothes[$liChunDayZhi]['衣服']}繫{$mangShenClothes[$liChunDayZhi]['腰帶']}，平梳兩髻{$mangShenHair[$liChunDayNaYin]['位置']}，{$earCover[$hourZhi]}，{$shoePantsSettings[$liChunDayNaYin]}，鞭杖用柳枝，長二尺四寸，五色醮染用{$ribbonKnot[$liChunDayZhi]}，$mangShenPosition。】";
    }
?>
</div></div></div>
<?php include 'footer.php'; ?>	
</body>	
</html>
