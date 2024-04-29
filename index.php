<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>2024農民曆</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Add some styles for the dynamically added content */
    .dynamically-added {
      color: red;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="text-center mt-4 mb-5">2024農民曆</h1>
    <div class="row">
      <div class="col-md-12">
<?php

require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

$year = 2024;

for ($month = 1; $month <= 12; $month++) {
    // Loop through each month
    echo "<h2>{$year}年 {$month}月</h2>";

    // Get the number of days in the current month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Create a Gregorian Date object
        $gregorianDate = new DateTime("{$year}-{$month}-{$day}");

        // Convert Gregorian date to lunar date using Lunar.php
        $lunar = Lunar::fromDate($gregorianDate);

        $solar = Solar::fromDate($gregorianDate);

        echo "<p>";
		echo "【陽曆：";
        echo $solar->getYear()."-";
        echo $solar->getMonth()."-";
        echo $solar->getDay()."】\n";
        echo "【農曆：";
        echo $lunar->getYearInChinese()."年";
        echo $lunar->getMonthInChinese()."月";
        echo $lunar->getDayInChinese()."】\n";

        if ($lunar->getJieQi()) {
            // Display as h3 if it's a solar term day
            echo "<h3>【".$lunar->getJieQi()."】</h3>";
        }
		if ($lunar->getDayJi()[0] === '诸事不宜') {
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
        echo "】<p>";
    }
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
