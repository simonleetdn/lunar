<?php
if(isset($_GET["year-month"]) && !empty($_GET["year-month"])) {
    list($year, $month) = explode('-', $_GET["year-month"]);
} else {
    $year = date("Y");
    $month = date("m");
}
?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $year."年".$month."月"; ?>農民曆</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;700&display=swap" rel="stylesheet">
	<style>
    /* Add some styles for the dynamically added content */
    .dynamically-added {
      color: red;
    }
		
body {
  font-family: "Noto Serif TC", serif;
  font-weight: 400;
  font-style: normal;
 padding-top: 70px;
}	
		
h1, h2, h3, .navbar-brand {
  font-family: "Noto Serif TC", serif;
  font-weight: 700;
  font-style: normal;
}	
	
.nav-link {
  cursor: pointer;
}
  </style>
</head>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-danger">
	  <div class="container-md">
    <a class="navbar-brand" href="/">農民曆（黃曆）</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a id="exall" class="nav-link">全部展開</a>
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

    // Get the number of days in the current month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Create a Gregorian Date object
        $gregorianDate = new DateTime("{$year}-{$month}-{$day}");

        // Convert Gregorian date to lunar date using Lunar.php
        $lunar = Lunar::fromDate($gregorianDate);
        $solar = Solar::fromDate($gregorianDate);
		$foto = Foto::fromLunar($lunar);
		$tao = Tao::fromLunar($lunar);
		echo "<hr/>";

		$Festivallist = $lunar->getFestivals();
		
		if ($Festivallist || $lunar->getJieQi()) {
                echo "<h3>";
        }
		
		if ($Festivallist) {
            foreach ($Festivallist as $s) {
				echo "【".$s."】";
			}
        }
		
        if ($lunar->getJieQi()) {
            // Display as h3 if it's a solar term day
            echo "【".$lunar->getJieQi()."】";
        }
		
		if ($Festivallist || $lunar->getJieQi()) {
                echo "</h3>";
        }
		
        echo "<div class='day' id='{$day}'><p>";
		if ($solar->getWeekInChinese() === '日') {
			echo '<span class="text-danger">';
        } else {
            echo '<span class="text-black">';
		}
		echo "【陽曆：";
        echo $solar->getYear()."-";
        echo $solar->getMonth()."-";
        echo $solar->getDay();
		echo "\n星期".$solar->getWeekInChinese()."】</span>";
        echo "【農曆：";
		$ly = $lunar->getYearInGanZhi();
        $lm = $lunar->getMonthInChinese();
        $ld = $lunar->getDayInChinese();
        echo $ly."年";
        echo $lm."月";
        echo $ld."】";

		$sf = $solar->getFestivals();
		if ($sf){
			$sfc = count($sf);
			echo "【";
			foreach ($sf as $s) {
  				echo $s;
				    if ($s < $sfc - 1) {
                		echo "\n";
					}
			}
			echo "】";
		}
		
		$ff = $lunar->getOtherFestivals();
		if ($ff){
			$ffc = count($ff);
			echo "【";
			foreach ($ff as $f) {
  				echo $f;
				    if ($f < $ffc - 1) {
                		echo "\n";
					}
			}
			echo "】";
		}
		
		$sanfu = $lunar->getFu();
		$shujiu = $lunar->getShuJiu();
		if($sanfu || $shujiu){
			echo "【".$sanfu.$shujiu."】";
		}
		
		if (in_array('諸事不宜', $lunar->getDayYi()) || in_array('諸事不宜', $lunar->getDayJi())) {
			echo '<span class="text-black">';
        } else {
            echo '<span class="text-danger">';
		}
        echo "【宜：";
        $yiList = $lunar->getDayYi();
        $yiCount = count($yiList);
        foreach ($yiList as $key => $s) {
            echo $s;
            if ($key < $yiCount - 1) {
                echo "\n"; // Add newline for all items except the last one
            }
        }
        echo "】</span>\n";
        echo "【忌：";
        $jiList = $lunar->getDayJi();
        $jiCount = count($jiList);
        foreach ($jiList as $key => $s) {
            echo $s;
            if ($key < $jiCount - 1) {
                echo "\n"; // Add newline for all items except the last one
            }
        }
        echo "】";
		// 詳細strat
		
		echo "<span class='d-none' id='detail{$day}'>";
		
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
if ($ttl){
    $ttlc = count($ttl);
    echo '<span class="text-danger">【';
    foreach ($ttl as $key => $f) {
        echo $f;
        if ($key != $ttlc - 1) {
            echo "\n";
        }
    }
    echo "】</span>";
}
		
		
		echo "【吉神方位：";
		echo "喜神".$lunar->getPositionXiDesc()."\n";
		echo "陽貴神".$lunar->getPositionYangGuiDesc()."\n";
		echo "陰貴神".$lunar->getPositionYinGuiDesc()."\n";
		echo "福神".$lunar->getPositionFuDesc()."\n";
		echo "財神".$lunar->getDayPositionCaiDesc();
		echo "】";
		
		echo "【日祿：".$lunar->getDayLu()."】";

		echo "【日沖".$lunar->getDayChongDesc()."\n煞".$lunar->getDaySha()."】";
		
		echo "【六曜：".$lunar->getLiuYao()."】";
		
		echo "【物候：".$lunar->getWuHou()."】";
		
		echo "【日納音：".$lunar->getDayNaYin()."】";
			
		echo "</span>";
		// 詳細end
		echo "<p></div>";
    }
?>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('year-month').addEventListener('change', function() {
        document.getElementById('year-month-form').submit();
    });
	
  // Function to toggle visibility of detail element
  function toggleDetailVisibility(day) {
    var detailElement = document.getElementById('detail' + day);
    if (detailElement.classList.contains('d-none')) {
      detailElement.classList.remove('d-none');
    } else {
      detailElement.classList.add('d-none');
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
let startX = 0;

// 监听触摸开始事件
document.addEventListener('touchstart', function(event) {
  touchStartX = event.touches[0].clientX;
});

// 监听触摸结束事件
document.addEventListener('touchend', function(event) {
  const touchEndX = event.changedTouches[0].clientX;
  const deltaX = touchEndX - touchStartX;
  
  if (Math.abs(deltaX) > 50) {
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
</body>
</html>
