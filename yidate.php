<?php
if(isset($_GET["year-yi"]) && !empty($_GET["year-yi"])) {
    list($year, $yi) = explode('-', $_GET["year-yi"]);
} else {
    $year = date("Y");
	$yi = "日查詢，請在清單選擇項目";
}
?>
<?php include 'metaseo.php'; ?>
  <title>農民曆<?php echo $year."年宜".$yi."的日子"; ?></title>
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

<?php
$menuItems = [
    '宗教' => [
        '祭祀', '祈福', '求嗣', '開光', '塑繪', '齋醮', '沐浴', '酬神', '造廟', '謝土', '出火', '雕刻'
    ],
    '婚嫁' => [
        '嫁娶', '納采', '納婿', '歸寧', '安床', '合帳', '冠笄', '訂盟', '進人口'
    ],
    '生活' => [
        '裁衣', '理髮', '整手足甲', '針灸', '求醫', '治病', '出行', '移徙', '會親友', '習藝', '入學'
    ],
    '喪葬' => [
        '修墳', '啟鉆', '破土', '安葬', '立碑', '成服', '除服', '開生墳', '合壽木', '入殮', '移柩', '普渡'
    ],
    '建造' => [
        '入宅', '安香', '安門', '修造', '起基', '動土', '上梁', '豎柱', '合脊', '造倉', '作竈', '解除'
    ],
    '土地' => [
        '拆卸', '破屋', '壞垣', '補垣', '伐木', '開柱眼', '開廁', '造倉', '塞穴', '平治道塗', '造橋', '築堤', '開池', '開渠', '掘井', '掃舍', '放水', '造畜稠', '修門', '定磉', '作梁', '修飾垣墻'      
    ],
    '商業' => [
        '架馬', '開市', '掛匾', '納財', '開倉', '置產', '雇庸', '出貨財', '安機械', '造車器', '經絡', '造船', '割蜜', '栽種', '取漁', '結網', '牧養', '安碓磑', '赴任', '立券', '交易'
    ],
    '藝術' => [
        '塑繪', '雕刻', '習藝', '裁衣'
    ],
    '其他' => [
        '納畜', '捕捉', '畋獵', '教牛馬', '斷蟻'
    ]
];
?>

		  
<?php foreach ($menuItems as $category => $items) : ?>
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" id="<?php echo strtolower($category); ?>Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $category; ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="<?php echo strtolower($category); ?>Dropdown">
            <?php foreach ($items as $item) : ?>
                <a class="dropdown-item" href="yidate.php?year-yi=<?php echo $year."-".$item ?>"><?php echo $item; ?></a>
            <?php endforeach; ?>
        </div>
    </li>
<?php endforeach; ?>





  
      </ul>
<form class="form-inline ml-auto" id="year-form" method="get">
    <label class="mr-2 text-light" for="year">選擇年份</label>
    <input type="number" id="year" name="year" value="<?php echo $year; ?>" class="form-control mr-2" />
</form>		
    </div>
	</div>	  
  </nav>
	<div class="container">
    <div class="row mb-3">
      <div class="col-md-12">

<?php
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

echo "<h2>【".$year."年宜".$yi."的日子】</h2>";
// 開始日期
$startDate = new DateTime("$year-01-01");

// 結束日期
$endDate = new DateTime("$year-12-31");

// 使用迴圈遍歷整年的日期
$currentDate = clone $startDate;
$counter = 0; // 計數器，用於每兩個項目換行
while ($currentDate <= $endDate) {
    // 將日期轉換為 Lunar 物件
    $lunar = Lunar::fromDate($currentDate);
    $solar = Solar::fromDate($currentDate);
    // 獲取當天的宜嫁娶資訊
    $dayYi = $lunar->getDayYi();
    // 檢查當天是否有宜嫁娶的資訊，如果有則列出陽曆和農曆日期
    if (in_array($yi, $dayYi)) {
        if ($counter % 2 == 0) {
            echo '<div class="row">'; // 開始新的一行
        }
        echo '<div class="col-md-12">';
		if ($solar->getWeekInChinese() === '六' || $solar->getWeekInChinese() === '日') {
			echo '<span class="text-danger">';
        } else {
            echo '<span class="text-black">';
		}
        echo "【陽曆：";
        echo $solar->getYear()."年";
        echo $solar->getMonth()."月";
        echo $solar->getDay();
        echo "日星期".$solar->getWeekInChinese();
        echo "\n◈\n農曆：";
        $ly = $lunar->getYearInGanZhi();
        $lm = $lunar->getMonthInChinese();
        $ld = $lunar->getDayInChinese();
        echo $ly."(".$lunar->getYearShengXiao().")年";
        echo $lm."月";
        echo $ld."\n◈\n";
		echo "沖：".$lunar->getDayChongDesc()."】";
        echo '</span></div>'; // 結束 col-md-6
        if ($counter % 2 == 1) {
            echo '</div>'; // 結束 row
        }
        $counter++;
    }
    // 移動到下一天
    $currentDate->modify('+1 day');
}
// 如果最後一行只有一個項目，要手動結束 row
if ($counter % 2 == 1) {
    echo '</div>'; // 結束 row
}
?>
		</div></div></div>
<script>
  // 監聽年份輸入框的變化事件
  document.getElementById('year').addEventListener('change', function() {
    // 獲取年份和宜嫁娶資訊
    var year = document.getElementById('year').value;
    var yi = '<?php echo $yi; ?>'; // 這裡直接使用 PHP 的 $yi 變量

    // 構建新的網址
    var newUrl = 'yidate.php?year-yi=' + year + '-' + yi;

    // 跳轉到新的網址
    window.location.href = newUrl;
  });
</script>
	
<?php include 'footer.php'; ?>	    	
</body>	
</html>
