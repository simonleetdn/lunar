<?php
if (isset($_GET["year"]) && !empty($_GET["year"])) {
    $year = (int)$_GET["year"];
} else {
    $year = date("Y");
}
$title = $year . "年節氣速查";
$description = "查詢" . $year . "年二十四節氣的準確日期時間。";
?>
<?php include 'metaseo.php'; ?>
<meta name="description" content="<?php echo $description; ?>">
<title><?php echo $title; ?></title>
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
            <a class="nav-link" href="lunardate.php">每月農民曆</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="yidate.php">每年宜日速查</a>
          </li>
          <li class="nav-item">
            <a id="ziwei" class="nav-link" href="ziwei.php">紫微斗數</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="calendar/">日曆(自動更新)</a>
          </li>
        </ul>
        <form class="form-inline ml-auto" id="year-form" method="get" action="jieqi.php">
          <div class="input-group">
            <div class="input-group-prepend">
              <label class="input-group-text bg-danger text-light" for="year">選擇年份</label>
            </div>
            <input type="number" id="year" name="year" value="<?php echo $year; ?>" class="form-control" style="width: 100px;">
            <div class="input-group-append">
              <button type="submit" class="btn btn-warning">查詢</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </nav>

  <div class="sticky-top" style="top: 50px; background-color: white; z-index: 1020;">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h2 id='page-title' class='mt-3 mb-3'><?php echo $year; ?>年 二十四節氣</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php
        require 'Lunar.php';
        use com\nlf\calendar\Lunar;

        // 獲取該年的第一個農曆物件以取得節氣表
        $lunar = Lunar::fromYmd($year, 1, 1);
        $jieqiTable = $lunar->getJieQiTable();

        // 確保節氣按時間順序顯示
        uksort($jieqiTable, function($a, $b) use ($jieqiTable) {
            return $jieqiTable[$a]->getJulianDay() <=> $jieqiTable[$b]->getJulianDay();
        });

        // 找到當前的節氣
        $currentJieQiName = null;
        if ($year == date("Y")) {
            $now = new DateTime();
            $previousSolar = null;
            foreach ($jieqiTable as $jieqiName => $solar) {
                $solarDateTime = new DateTime($solar->toYmdHms());
                if ($solarDateTime > $now) {
                    break;
                }
                $currentJieQiName = $jieqiName;
            }
        }

        // 將節氣的英文/拼音 key 轉換為中文名稱
        if (!function_exists('convertJieQiName')) {
            function convertJieQiName($name) {
                $map = [
                    'DONG_ZHI' => '冬至', 'DA_HAN' => '大寒', 'XIAO_HAN' => '小寒',
                    'LI_CHUN' => '立春', 'DA_XUE' => '大雪', 'YU_SHUI' => '雨水',
                    'JING_ZHE' => '驚蟄'
                ];
                if (array_key_exists($name, $map)) {
                    return $map[$name];
                }
                // 如果不在 map 中，表示它本身就是中文
                return $name;
            }
        }

        foreach ($jieqiTable as $jieqiName => $solar) {
            $JieQi = convertJieQiName($jieqiName);
            $isCurrent = ($currentJieQiName === $jieqiName);
            if ($isCurrent) {
                echo '<div class="day highlight">';
            }

            echo "<hr/>";
            // 使用我們在上方定義的 convertJieQiName 函數
            $lunarDate = $solar->getLunar(); // 獲取農曆日期以供顯示
            echo '<div class="row"><div class="col-md-12"><h3 class="float-left">【' . $JieQi . '】</h3>【農曆：' . $lunarDate->getMonthInChinese() . '月' . $lunarDate->getDayInChinese() . '】';
            include 'jieqi_detail.php';
echo '</div></div>';
            if ($isCurrent) {
                echo '</div>';
            }
        }
        ?>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
</body>
</html>