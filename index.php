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
		
  </style>
</head>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-danger">
	  <div class="container-md">
    <a class="navbar-brand" href="#">農民曆（黃曆）</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">關於</a>
        </li>
      </ul>
      <form class="form-inline ml-auto" method="get">
        <label class="mr-2 text-light" for="year-month">選擇其他年月份：</label>
        <input type="month" id="year-month" name="year-month" value="<?php echo $year.'-'.$month; ?>" class="form-control mr-2" />
        <button type="submit" class="btn btn-warning">確定</button>
      </form>
    </div>
	</div>	  
  </nav>
	<div class="container">
    <div class="row">
      <div class="col-md-12">	  
<?php

require 'Lunar.php';

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

        echo "<p>";
		if ($solar->getWeekInChinese() === '日') {
			echo '<span class="text-danger">';
        } else {
            echo '<span class="text-black">';
		}
		echo "【陽曆：";
        echo $solar->getYear()."-";
        echo $solar->getMonth()."-";
        echo $solar->getDay();
		echo "星期".$solar->getWeekInChinese()."】</span>\n";
        echo "【農曆：";
        echo $lunar->getYearInChinese()."年";
        echo $lunar->getMonthInChinese()."月";
        echo $lunar->getDayInChinese()."】\n";

        if ($lunar->getJieQi()) {
            // Display as h3 if it's a solar term day
            echo "<h3>【".$lunar->getJieQi()."】</h3>";
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
		echo "【財神：".$lunar->getDayPositionCaiDesc()."】<p>";
    }
?>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
