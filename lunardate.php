<?php
if(isset($_GET["year-month"]) && !empty($_GET["year-month"])) {
    list($year, $month) = explode('-', $_GET["year-month"]);
	$title = $year."年".$month."月農民曆";
} else {
    $year = date("Y");
    $month = date("m");
	$title = "每月農民曆";
}
?>
<?php include 'metaseo.php'; ?>
  <title><?php echo $title; ?></title>
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
          <a id="exall" class="nav-link">全部展開</a>
        </li>
		  <li class="nav-item">
          <a id="yidate" class="nav-link" href="yidate.php">每年宜日速查</a>
        </li>
      </ul>
<form class="form-inline ml-auto" id="year-month-form" method="get">
    <label class="mr-2 text-light" for="year-month">選擇月份：</label>
    <input type="month" id="year-month" name="year-month" value="<?php echo $year.'-'.$month; ?>" class="form-control mr-2" />
</form>
    </div>
	</div>	  
  </nav>
	<div class="container">
    <div class="row">
      <div class="col-md-12">	  
<?php

require 'Lunar.php';

use com\nlf\calendar\Foto;
use com\nlf\calendar\Tao;		  
use com\nlf\calendar\LunarYear;
use com\nlf\calendar\util\HolidayUtil;
use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

    echo "<h2 class='mt-6'>{$year}年 {$month}月</h2>";
echo '<div class="alert alert-warning" role="alert">歡迎使用本網站查詢農民曆。以下是操作方式的簡單說明：點擊日期列表可展開當日更詳細資訊，左/右鍵或手機左/右滑可切換月份，右上角選單選擇其他年份月份，點擊農民曆標題回到當前月份。</div>';  
    // Get the number of days in the current month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Create a Gregorian Date object
        $gregorianDate = new DateTime("{$year}-{$month}-{$day}");

        // Convert Gregorian date to lunar date using Lunar.php
        $lunar = Lunar::fromDate($gregorianDate);
        $solar = Solar::fromDate($gregorianDate);
		$sy = $solar->getYear();
        $sm = $solar->getMonth();
		$szm = sprintf('%02d', $solar->getMonth());
		$szd = sprintf('%02d', $solar->getDay());
        $sd = $solar->getDay();
		$td = date('Ynj');
		$foto = Foto::fromLunar($lunar);
		$tao = Tao::fromLunar($lunar);
		echo "<hr/>";

		
		
		echo "<div class='day";
		if ($sy.$sm.$sd === $td) {
			echo " bg-warning";
        }
		echo "' id='{$day}'>";
		
		$Festivallist = $lunar->getFestivals();
		
		
		if ($Festivallist) {
            foreach ($Festivallist as $s) {
				echo '<h3 class="float-left">【'.$s.'】</h3>';
			}
        }
		
		
		if ($solar->getWeekInChinese() === '六' || $solar->getWeekInChinese() === '日') {
			echo '<span class="text-danger">';
        } else {
            echo '<span class="text-black">';
		}
        echo "【陽曆：".$sy."年".$sm."月".$sd."日";
		echo "，星期".$solar->getWeekInChinese()."】</span>";
		$ly = $lunar->getYearInGanZhi();
		$ls = $lunar->getYearShengXiao();
        $lm = $lunar->getMonthInChinese();
        $ld = $lunar->getDayInChinese();
        echo "【農曆：".$ly."(".$ls.")"."年".$lm."月".$ld."】";

$sf = $solar->getFestivals();
if (!empty($sf)) {
    echo "【" . implode("， ", $sf) . "】";
}

$ff = $lunar->getOtherFestivals();
if (!empty($ff)) {
    echo "【" . implode("， ", $ff) . "】";
}

		
		$sanfu = $lunar->getFu();
		$shujiu = $lunar->getShuJiu();
		if($sanfu || $shujiu){
			echo "【".$sanfu.$shujiu."】";
		}

		
// 月破大凶日		
$xsyq = $lunar->getDayXiongSha();
$jsyq = $lunar->getDayJiShen();

$xiongShaCheck = [
    "月破大耗日吉事少取" => ["月破", "大耗"],
    "往亡日" => ["往亡"]
];

$jiShenCheck = [
    "天德合日" => ["天德合"],
    "月德合日" => ["月德合"],
    "天德日" => ["天德"],
    "月德日" => ["月德"]
];

// 檢查凶煞日
foreach ($xiongShaCheck as $message => $conditions) {
    $matched = true;
    foreach ($conditions as $condition) {
        if (!in_array($condition, $xsyq)) {
            $matched = false;
            break;
        }
    }
    if ($matched) {
        echo "【{$message}】";
    }
}

// 檢查吉神日
foreach ($jiShenCheck as $message => $conditions) {
    foreach ($conditions as $condition) {
        if (in_array($condition, $jsyq)) {
            echo "【{$message}】";
            break;
        }
    }
}	

// 刀砧日
// 獲取四季的節氣日期
$lichun = $lunar->getJieQiTable()['立春']->toYmdHms();
$lixia = $lunar->getJieQiTable()['立夏']->toYmdHms();
$liqiu = $lunar->getJieQiTable()['立秋']->toYmdHms();
$lidong = $lunar->getJieQiTable()['立冬']->toYmdHms();

// 判斷當前日期是否為刀砧日
$daoZhen = false;
$lrdz = $lunar->getDayZhi(); // 修正為 getDayZhi 以獲取日地支
// 判斷日期所屬季節並檢查對應的地支
if ($solar->toYmdHms() >= $lichun && $solar->toYmdHms() < $lixia) {
    // 春季：立春到立夏
    if (in_array($lrdz, ['亥', '子'])) {
        $daoZhen = true;
    }
} elseif ($solar->toYmdHms() >= $lixia && $solar->toYmdHms() < $liqiu) {
    // 夏季：立夏到立秋
    if (in_array($lrdz, ['寅', '卯'])) {
        $daoZhen = true;
    }
} elseif ($solar->toYmdHms() >= $liqiu && $solar->toYmdHms() < $lidong) {
    // 秋季：立秋到立冬
    if (in_array($lrdz, ['巳', '午'])) {
        $daoZhen = true;
    }
} else {
    // 冬季：立冬到下一年的立春
    if (in_array($lrdz, ['申', '酉'])) {
        $daoZhen = true;
    }
}

// 輸出結果
if ($daoZhen) {
    echo "【刀砧日】";
}

		
		$yiList = $lunar->getDayYi();
$jiList = $lunar->getDayJi();

// Determine the text color based on the presence of '諸事不宜' in the lists
$textColor = (in_array('諸事不宜', $yiList) || in_array('餘事勿取', $yiList) || in_array('諸事不宜', $jiList) || in_array('嫁娶', $jiList)) ? 'text-black' : 'text-danger';

// Output "宜" and "忌" with their respective lists
echo "<span class='$textColor'>【宜：" . implode("，", $yiList) . "】</span>";
echo "【忌：" . implode("，", $jiList) . "】";
echo "【沖：".$lunar->getDayChongDesc()."】【煞：".$lunar->getDaySha()."】";
	
		// 詳細strat
		if ($sy.$sm.$sd === $td) {
		echo "<span class='' id='detail{$day}'>";
        }else{		
		echo "<span class='d-none' id='detail{$day}'>";
		}
								//echo "【佛曆：".$foto."】";
		
//$ftl = $foto->getOtherFestivals();;
//if ($ftl){
//    $ftlc = count($ftl);
//    echo "【";
//    foreach ($ftl as $key => $f) {
//        echo $f;
//        if ($key != $ftlc - 1) {
//            echo "\n"; 
//        }
//    }
//    echo "】";
//}
		
						//echo "【道曆：".$tao."】";
		
$ttl = $tao->getFestivals();

if (!empty($ttl)) {
    echo '<span class="text-danger">【' . implode("\n", $ttl) . '】</span>';
}

		echo "【吉神：";
		echo "喜神".$lunar->getPositionXiDesc()."，";
		echo "陽貴神".$lunar->getPositionYangGuiDesc()."，";
		echo "陰貴神".$lunar->getPositionYinGuiDesc()."，";
		echo "福神".$lunar->getPositionFuDesc()."，";
		echo "財神".$lunar->getDayPositionCaiDesc();
		echo "】";
		
		echo "【胎神：".$lunar->getDayPositionTai()."】";
		
		echo "【太歲：".$lunar->getDayPositionTaiSuiDesc()."】";
		
		//$jsyq = $lunar->getDayJiShen();
if (!empty($jsyq)) {
    echo "【吉神宜趨：" . implode("，", $jsyq) . "】";
}
		
//		$xsyq = $lunar->getDayXiongSha();
if (!empty($xsyq)) {
    echo "【凶神宜忌：" . implode("，", $xsyq) . "】";
}
		
		echo "【日祿：".$lunar->getDayLu()."】";
		
		echo "【六曜：".$lunar->getLiuYao()."】";
		
		echo "【物候：".$lunar->getWuHou()."】";
		
$dayGan = $lunar->getDayGan(); // 獲取日天干
$dayZhi = $lunar->getDayZhi(); // 獲取日地支
$monthGanZhi = $lunar->getMonthInGanZhi(); // 獲取日干支
$dayGanZhi = $lunar->getDayInGanZhi(); // 獲取日干支

echo "【干支：".$monthGanZhi."月".$dayGanZhi."日】";	

		echo "【納音：".$lunar->getDayNaYin()."】";
		
		echo "【九星：".$lunar->getDayNineStar()->getNumber().$lunar->getDayNineStar()->getColor()."】";
		
		echo "【宿：".$lunar->getXiu()."】";
	//	echo "【二十八動物：".$lunar->getAnimal()."】";
	//	echo "【二十八星宿吉凶：".$lunar->getXiuLuck()."】";
	//	echo "【二十八宿歌诀：".$lunar->getXiuSong()."】";
		
		echo "【建除：".$lunar->getZhiXing()."】";
		
		echo "【四宮：".$lunar->getGong()."】";
		echo "【神獸：".$lunar->getShou()."】";
		echo "【天神：".$lunar->getDayTianShenLuck()."，".$lunar->getDayTianShen()."】";
	//	echo "【空亡：".$lunar->getEightChar()->getDayXunKong()."】";
		
	//	echo "【彭祖百忌：".$lunar->getPengZuGan()."\n".$lunar->getPengZuZhi()."】";
		
		// 对应的时辰列表及其时间范围
$timePeriodList = [
    '子' => [23, 1],
    '丑' => [1, 3],
    '寅' => [3, 5],
    '卯' => [5, 7],
    '辰' => [7, 9],
    '巳' => [9, 11],
    '午' => [11, 13],
    '未' => [13, 15],
    '申' => [15, 17],
    '酉' => [17, 19],
    '戌' => [19, 21],
    '亥' => [21, 23]
];

// 迭代一天中的每个时辰
foreach ($timePeriodList as $timePeriod => $hours) {
    // 创建一个DateTime对象并设置到该时辰的第一个小时
    $gregorianDate->setTime($hours[0], 0, 0);
    
    // 获取该时辰的农历信息
    $lunarhour = Lunar::fromDate($gregorianDate);
    
    // 获取宜和忌沖剎
	$timechong = $lunarhour->getTimeChongDesc();
	$timesha = $lunarhour->getTimeSha();
    $yiList = $lunarhour->getTimeYi();
    $jiList = $lunarhour->getTimeJi();
echo "<br/>" . ($lunarhour->getTimeTianShenLuck() === "吉" ? '<span class="text-danger">' : '') . "【$timePeriod (" . sprintf('%02d', $hours[0]) . "-" . sprintf('%02d', $hours[1]) . ")◈";
echo "天神：" . $lunarhour->getTimeTianShenLuck() . "，" . $lunarhour->getTimeTianShen() . "◈";
echo "沖：" . $timechong . "◈";
echo "煞：" . $timesha . "◈";
echo "宜：" . implode('，', $yiList) . "◈";
echo "忌：" . implode('，', $jiList) . "】" . ($lunarhour->getTimeTianShenLuck() === "吉" ? '</span>' : '');

}		
		echo "</span>";
		// 詳細end
		
		
		
		$JieQi = $lunar->getJieQi();
		
		if ($JieQi) {

            echo '<hr/><h3 class="float-left">【'.$JieQi.'】</h3>';

			$jieqidatetime = $lunar->getJieQiTable()[$JieQi]->toYmdHms(); // 假設這是您得到的時間字符串
			$jieqidatetime = substr($jieqidatetime, 0, 16); // 去除秒數，只保留年月日時分
			// 使用 date 函數將時間字符串轉換為指定格式
			$formatted_jieqidatetime = date("Y年m月d日 H:i", strtotime($jieqidatetime));
			echo "【時間：".$formatted_jieqidatetime."】";

// 設定臺灣中心點的經緯度
$latitude = 23.6978;
$longitude = 120.9605;

// 將 Solar 物件轉換為時間戳
$solardate = new DateTime();
$solardate->setDate($sy, $szm, $szd);

// 獲取日出日落時間信息
$sun_info = date_sun_info($solardate->getTimestamp(), $latitude, $longitude);

// 將時間格式轉換為人類可讀形式
$sunrise = date("H:i", $sun_info['sunrise']);
$sunset = date("H:i", $sun_info['sunset']);

// 輸出結果
echo "【日出：".$sunrise."】";
echo "【日沒：".$sunset."】";
			
			
			// 定義每個節氣對應的太陽經過的度數
$jieqi_info = [
    '立春' => ['度數' => '315', '意義' => '氣候開始轉暖，春天開始'],
    '雨水' => ['度數' => '330', '意義' => '降雨增多，有利於農作物生長'],
    '驚蟄' => ['度數' => '345', '意義' => '天氣漸熱，動物開始活動'],
    '春分' => ['度數' => '0', '意義' => '白晝和黑夜等長，春天進入中期'],
    '清明' => ['度數' => '15', '意義' => '氣候溫暖，適宜掃墓祭祖'],
    '穀雨' => ['度數' => '30', '意義' => '雨生百穀，開始穀物收穫'],
    '立夏' => ['度數' => '45', '意義' => '夏季開始，炎熱多雨'],
    '小滿' => ['度數' => '60', '意義' => '夏熟作物籽粒開始飽滿'],
    '芒種' => ['度數' => '75', '意義' => '夏熟作物進入收穫季節'],
    '夏至' => ['度數' => '90', '意義' => '白天最長'],
    '小暑' => ['度數' => '105', '意義' => '天氣炎熱'],
    '大暑' => ['度數' => '120', '意義' => '天氣最熱，正值盛夏'],
    '立秋' => ['度數' => '135', '意義' => '天氣漸涼，秋天的開始'],
    '處暑' => ['度數' => '150', '意義' => '氣溫逐漸下降，秋天即將到來'],
    '白露' => ['度數' => '165', '意義' => '天氣轉涼，濕氣逐漸凝結為露水'],
    '秋分' => ['度數' => '180', '意義' => '白天和黑夜等長，秋天進入中期'],
    '寒露' => ['度數' => '195', '意義' => '氣溫進一步下降，露水結霜'],
    '霜降' => ['度數' => '210', '意義' => '天氣更冷，容易結霜'],
    '立冬' => ['度數' => '225', '意義' => '天氣轉冷，冬天的開。'],
    '小雪' => ['度數' => '240', '意義' => '水氣轉為雪，初雪降臨'],
    '大雪' => ['度數' => '255', '意義' => '降雪量顯著增多'],
    '冬至' => ['度數' => '270', '意義' => '白天最短'],
    '小寒' => ['度數' => '285', '意義' => '天氣寒冷'],
    '大寒' => ['度數' => '300', '意義' => '天氣寒冷極致，寒冷的頂峰'],
];

			
// 輸出太陽位於黃經的度數和相應的節氣意義
echo '<br />【太陽位於黃經'.$jieqi_info[$JieQi]['度數'].'度】【'.$jieqi_info[$JieQi]['意義'].'】';			
			
        }
		
		
		echo "</div>";
    }
?>
      </div>
    </div>
  </div>
<script>
    document.getElementById('year-month').addEventListener('change', function() {
        document.getElementById('year-month-form').submit();
    });
	
  // Function to toggle visibility of detail element
  function toggleDetailVisibility(day) {
    var detailElement = document.getElementById('detail' + day);
    if (detailElement.classList.contains('d-none')) {
      detailElement.classList.remove('d-none');
      document.getElementById(day).classList.add('bg-warning');
    } else {
      detailElement.classList.add('d-none');
      document.getElementById(day).classList.remove('bg-warning');
    }
  }

  // Add event listeners to all elements with class 'day'
  var dayElements = document.getElementsByClassName('day');
  for (var i = 0; i < dayElements.length; i++) {
    dayElements[i].addEventListener('click', function() {
      // Extract the day from the element's ID
      var day = this.id;
      toggleDetailVisibility(day);
    });
  }
	
	// 左右切換月份
// 获取 URL 中的 year-month 参数值，如果没有则使用当前年月
const urlParams = new URLSearchParams(window.location.search);
let currentYearMonth = urlParams.get('year-month');

if (!currentYearMonth) {
  // 如果没有 year-month 参数，则使用当前年月
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth() + 1; // 月份从 0 开始计数，需要加 1
  currentYearMonth = `${currentYear}-${currentMonth.toString().padStart(2, '0')}`;
}

// 将当前年月字符串转换为 JavaScript Date 对象
const currentDate = new Date(currentYearMonth);

// 记录触摸起始位置和滑动起始位置
let touchStartX = 0;
let touchStartY = 0;
let startX = 0;
let startY = 0;

// 监听触摸开始事件
document.addEventListener('touchstart', function(event) {
  touchStartX = event.touches[0].clientX;
  touchStartY = event.touches[0].clientY; // 记录Y坐标
});

// 监听触摸结束事件
document.addEventListener('touchend', function(event) {
  const touchEndX = event.changedTouches[0].clientX;
  const touchEndY = event.changedTouches[0].clientY;
  const deltaX = touchEndX - touchStartX;
  const deltaY = touchEndY - touchStartY;
  
  if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
    if (deltaX > 0) {
      // 向右滑动，调用 changeMonth 函数向右跳转一个月份
      changeMonth(1);
    } else {
      // 向左滑动，调用 changeMonth 函数向左跳转一个月份
      changeMonth(-1);
    }
  }
});


// 监听键盘左右箭头键事件
document.addEventListener('keydown', function(event) {
  if (event.keyCode === 37) {
    // 左箭头键，调用 changeMonth 函数向左滑动
    changeMonth(-1);
  } else if (event.keyCode === 39) {
    // 右箭头键，调用 changeMonth 函数向右滑动
    changeMonth(1);
  }
});

// 创建函数用于增减月份
function changeMonth(offset) {
  // 添加指定月份的偏移量
  currentDate.setMonth(currentDate.getMonth() + offset);
  
  // 格式化新的年月字符串
  const newYear = currentDate.getFullYear();
  const newMonth = currentDate.getMonth() + 1; // 月份从 0 开始计数，需要加 1
  const newYearMonth = `${newYear}-${newMonth.toString().padStart(2, '0')}`;
  
  // 构建新的 URL 并跳转
  const newUrl = `?year-month=${newYearMonth}`;
  window.location.href = newUrl;
}

//全部展開
// 获取全部展开按钮元素
const expandBtn = document.querySelector('#exall');

// 获取需要展开的元素集合
const elementsToExpand = document.querySelectorAll('.d-none');

// 监听展开按钮点击事件
expandBtn.addEventListener('click', function(event) {
  // 遍历需要展开的元素集合，并移除所有元素的 d-none 类
  elementsToExpand.forEach(element => {
    element.classList.remove('d-none');
  });
});
	
</script>	
<?php include 'footer.php'; ?>	
</body>	
</html>
