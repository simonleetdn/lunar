<?php
// 檢查是否有 year-month 參數並設定年份和月份
if (isset($_GET["year-month"]) && !empty($_GET["year-month"])) {
    list($year, $month) = explode('-', $_GET["year-month"]);
    $title = $year . "年" . $month . "月農民曆";
} else {
    $year = date("Y");
    $month = date("m");
    $title = "每月農民曆";
}
?>
<?php include 'metaseo.php'; ?>
<meta name="description" content="在這裡查詢每個月的農曆日期！我們提供精確的農民曆資訊，幫助您掌握日常生活和重要節日的日期。">
<link rel="canonical" href="https://lunar.ioi.tw/" />
<title><?php echo $title; ?></title>
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
            <a id="yidate" class="nav-link" href="yidate.php">每年宜日速查</a>
          </li>
		  <li class="nav-item">
            <a id="calendar" class="nav-link" href="/calendar/">日曆(自動更新)</a>
          </li>
          <li class="nav-item">
            <a id="yuangang" class="nav-link" href="yuangang.php?birthDateTime=<?php echo date('Y-m-d\TH:i'); ?>">稱骨算命</a>
          </li>
          <li class="nav-item">
            <a id="exall" class="nav-link">展開全部</a>
          </li>
        </ul>
        <form class="form-inline ml-auto" id="year-month-form" method="get" action="lunardate.php">
          <div class="input-group">
            <div class="input-group-prepend">
              <label class="input-group-text bg-danger text-light" for="year-month">選擇月份</label>
            </div>
            <input type="month" id="year-month" name="year-month" value="<?php echo $year . '-' . $month; ?>" class="form-control">
            <div class="input-group-append">
              <button type="submit" id="submitBtn" class="btn btn-warning">進呈</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </nav>
  <div class="nav-button prev" id="prev-button">︻<br>上<br>個<br>月<br>︼</div>
  <div class="nav-button next" id="next-button">︻<br>下<br>個<br>月<br>︼</div>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php
        // 引入 Lunar.php 所需的類別
        require 'Lunar.php';
        use com\nlf\calendar\Foto;
        use com\nlf\calendar\Tao;
        use com\nlf\calendar\LunarYear;
        use com\nlf\calendar\Lunar;
        use com\nlf\calendar\Solar;

        echo "<h2 id='page-title' class='mt-6'>{$year}年 {$month}月</h2>";

        // 獲取當月天數
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // 迭代每一天
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $gregorianDate = new DateTime("{$year}-{$month}-{$day}");
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
            $ly = $lunar->getYearInGanZhi();
            $lyg = $lunar->getYearGan();
            $lyz = $lunar->getYearZhi();
            $ls = $lunar->getYearShengXiao();
            $lm = $lunar->getMonthInChinese();
            $ld = $lunar->getDayInChinese();
            $lunarYear = LunarYear::fromYear($sy);
            $Festivallist = $lunar->getFestivals();
            $dayGan = $lunar->getDayGan();
            $dayZhi = $lunar->getDayZhi();
            $monthGanZhi = $lunar->getMonthInGanZhi();
            $dayGanZhi = $lunar->getDayInGanZhi();
            echo "<hr/>";
            
            // 顯示新年信息
            if (in_array("春節", $Festivallist)) {
                include_once("dimujing.php");
                include_once("springoxcon.php");
                echo '<div id="newyear" class="text-danger newyear"><h3 class="float-left">';
                echo '【歲次' . $ly . '肖' . $ls . '】</h3>';
                echo '【年太歲：' . $taishui_mapping[$ly] . '星君，' . $lunarYear->getPositionTaiSuiDesc() . '方】';
                echo '【三元：' . $lunarYear->getYuan() . '】';
                echo '【九運：' . $lunarYear->getYun() . '】';
                echo '【年納音：' . $lunar->getYearNaYin() . '】';
                echo '【年九星：' . $lunar->getYearNineStar() . '】';
                echo '【皇帝地母經：' . $dimujing_mapping[$ly] . '】';
                
                // 定義地支對應的姑把蠶規則
                $gubacanRules = [
                    '寅' => '一姑把蠶',
                    '申' => '一姑把蠶',
                    '巳' => '一姑把蠶',
                    '亥' => '一姑把蠶',
                    '子' => '二姑把蠶',
                    '午' => '二姑把蠶',
                    '卯' => '二姑把蠶',
                    '酉' => '二姑把蠶',
                    '辰' => '三姑把蠶',
                    '戌' => '三姑把蠶',
                    '丑' => '三姑把蠶',
                    '未' => '三姑把蠶',
                ];

                // 蠶食幾葉
                function convertToChinese($day) {
                    $chineseDays = ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"];
                    return $chineseDays[$day - 1];
                }

                $canLunar = $lunar;
                $daysToAdd = 0;

                while (mb_substr($canLunar->getDayNaYin(), 2, 1) !== "木") {
                    $canLunar = $lunar->next($daysToAdd);
                    $daysToAdd++;
                }

                $canShiJiYe = convertToChinese($canLunar->getDay());

                echo '【' . $lunarYear->getZhiShui() . '，' . $lunarYear->getDeJin() . '，' . $lunarYear->getGengTian() . '，' . $gubacanRules[$lyz] . '，蠶食' . $canShiJiYe . '葉】';
                echo '【春牛芒神服色：' . $oxcontent . '】';
                echo '</div><hr/>';
            }

            echo "<div class='day";

            if ($sy . $sm . $sd === $td) {
                echo " highlight";
            }
            echo "' id='{$day}'>";

            if ($Festivallist) {
                foreach ($Festivallist as $s) {
                    echo '<h3 class="float-left">【' . $s . '】</h3>';
                }
            }

            if ($solar->getWeekInChinese() === '六' || $solar->getWeekInChinese() === '日') {
                echo '<span class="text-danger">';
            } else {
                echo '<span class="text-black">';
            }
            echo '<h3 class="float-left">' . $sd . '</h3>';
            echo "【星期" . $solar->getWeekInChinese() . "】</span>";

            echo "【農曆：" . $ly . "(" . $ls . ")" . "年" . $lm . "月" . $ld . "】";
            
            if (!empty($ttl)) {
                echo '<span class="text-danger">【' . implode("，", $ttl) . '】</span>';
            }
            
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
            if ($sanfu || $shujiu) {
                echo "【" . $sanfu . $shujiu . "】";
            }

            // 檢查凶煞日
            $xsyq = $lunar->getDayXiongSha();
            $jsyq = $lunar->getDayJiShen();

            $xiongShaCheck = [
                "月破大耗日吉事少取" => ["月破", "大耗"],
                "往亡日" => ["往亡"]
            ];

            $jiShenCheck = [
                "天赦日" => ["天赦"],
                "天德合日" => ["天德合"],
                "月德合日" => ["月德合"],
                "天德日" => ["天德"],
                "月德日" => ["月德"]
            ];

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

            // 定義五陽干和五陰干對應的歲德
            $suiDeDays = [
                '五陽干' => ['甲' => '甲', '丙' => '丙', '戊' => '戊', '庚' => '庚', '壬' => '壬'],
                '五陰干' => ['乙' => '庚', '丁' => '壬', '己' => '甲', '辛' => '丙', '癸' => '戊'],
            ];

            $suiDeHeDays = [
                '五陽干' => ['甲' => '己', '丙' => '辛', '戊' => '癸', '庚' => '乙', '壬' => '丁'],
                '五陰干' => ['乙' => '乙', '丁' => '丁', '己' => '己', '辛' => '辛', '癸' => '癸'],
            ];

            $isYangGan = in_array($lyg, ['甲', '丙', '戊', '庚', '壬']);
            $isYinGan = in_array($lyg, ['乙', '丁', '己', '辛', '癸']);

            if ($isYangGan && $dayGan === $lyg) {
                echo "【歲德日】";
            } elseif ($isYinGan) {
                $suiDe = $suiDeDays['五陰干'][$lyg];
                if ($dayGan === $suiDe) {
                    echo "【歲德日】";
                }
            }

            if ($isYinGan && $dayGan === $lyg) {
                echo "【歲德合日】";
            } elseif ($isYangGan) {
                $suiDeHe = $suiDeHeDays['五陽干'][$lyg];
                if ($dayGan === $suiDeHe) {
                    echo "【歲德合日】";
                }
            }

            foreach ($jiShenCheck as $message => $conditions) {
                foreach ($conditions as $condition) {
                    if (in_array($condition, $jsyq)) {
                        echo "【{$message}】";
                        break;
                    }
                }
            }

            // 刀砧日麒麟日
            $lichun = $lunar->getJieQiTable()['立春']->toYmdHms();
            $lixia = $lunar->getJieQiTable()['立夏']->toYmdHms();
            $liqiu = $lunar->getJieQiTable()['立秋']->toYmdHms();
            $lidong = $lunar->getJieQiTable()['立冬']->toYmdHms();
            $daoZhen = false;
            $qiLin = false;
            $fengHuang = false;
            $lrdz = $lunar->getDayZhi();
            $lqlrxiu = $lunar->getXiu();

            if ($solar->toYmdHms() >= $lichun && $solar->toYmdHms() < $lixia) {
                if (in_array($lrdz, ['亥', '子'])) {
                    $daoZhen = true;
                }
                if ($lqlrxiu == "井") {
                    $qiLin = true;
                }
                if ($lqlrxiu == "危") {
                    $fengHuang = true;
                }
            } elseif ($solar->toYmdHms() >= $lixia && $solar->toYmdHms() < $liqiu) {
                if (in_array($lrdz, ['寅', '卯'])) {
                    $daoZhen = true;
                }
                if ($lqlrxiu == "尾") {
                    $qiLin = true;
                }
                if ($lqlrxiu == "昴") {
                    $fengHuang = true;
                }
            } elseif ($solar->toYmdHms() >= $liqiu && $solar->toYmdHms() < $lidong) {
                if (in_array($lrdz, ['巳', '午'])) {
                    $daoZhen = true;
                }
                if ($lqlrxiu == "牛") {
                    $qiLin = true;
                }
                if ($lqlrxiu == "胃") {
                    $fengHuang = true;
                }
            } else {
                if (in_array($lrdz, ['申', '酉'])) {
                    $daoZhen = true;
                }
                if ($lqlrxiu == "壁") {
                    $qiLin = true;
                }
                if ($lqlrxiu == "畢") {
                    $fengHuang = true;
                }
            }

            if ($daoZhen) {
                echo "【刀砧日】";
            }
            if ($qiLin) {
                echo "【麒麟日】";
            }
            if ($fengHuang) {
                echo "【鳳凰日】";
            }

            // 勿探病
            $avoidDays = ["壬寅", "壬午", "庚午", "甲寅", "乙卯", "己卯"];
            if (in_array($dayGanZhi, $avoidDays)) {
                echo "【勿探病】";
            }

            // 正八座日
            $zhengBaRules = [
                '子' => '癸酉',
                '丑' => '甲戌',
                '寅' => '丁亥',
                '卯' => '甲子',
                '辰' => '乙丑',
                '巳' => '甲寅',
                '午' => '丁卯',
                '未' => '甲辰',
                '申' => '己巳',
                '酉' => '甲午',
                '戌' => '丁未',
                '亥' => '甲申',
            ];
            if ($dayGanZhi === $zhengBaRules[$lyz]) {
                echo "【正八座日】";
            }

            // 月相資料
            include_once("moonphases.php");
            if (array_key_exists($sy . "-" . $sm . "-" . $sd, $moonPhases)) {
                $phase = $moonPhases[$sy . "-" . $sm . "-" . $sd]["phase"];
                $time = $moonPhases[$sy . "-" . $sm . "-" . $sd]["time"];
                echo "【" . $phase . "：" . $time . "】";
            }

            $ttl = $tao->getFestivals();

            echo "【干支：" . $monthGanZhi . "月" . $dayGanZhi . "日】";
            echo "【納音：" . $lunar->getDayNaYin() . "】";

            // 伏羲八卦
            $bagua_summer = ['坤', '艮', '坎', '巽', '震', '離', '兌', '乾'];
            $bagua_winter = ['乾', '兌', '離', '震', '巽', '坎', '艮', '坤'];

            $summerCome = $lunar->getJieQiTable()['夏至']->toYmd();
            $winterCome = $lunar->getJieQiTable()['冬至']->toYmd();

            $target_date = new DateTime($solar);
            $start_date_summer = new DateTime($summerCome);
            $start_date_winter = new DateTime($winterCome);

            echo "【八卦：";
            if ($target_date < $start_date_summer) {
                $interval_winter = $start_date_winter->diff($target_date)->days;
                $bagua_winter_index = $interval_winter % count($bagua_winter);
                $winter_bagua = $bagua_winter[$bagua_winter_index];
                echo $winter_bagua;
            } else {
                $interval_summer = $start_date_summer->diff($target_date)->days;
                $bagua_summer_index = $interval_summer % count($bagua_summer);
                $summer_bagua = $bagua_summer[$bagua_summer_index];
                echo $summer_bagua;
            }
            echo "】";

            // 玄空五行八卦六十四掛
            $trigrams = [
                "甲子" => "1☷一",
                "乙丑" => "3☲六",
                "丙寅" => "2☴四",
                "丁卯" => "6☶九",
                "戊辰" => "9☰六",
                "己巳" => "8☳二",
                "庚午" => "8☳九",
                "辛未" => "9☰三",
                "壬申" => "1☷七",
                "癸酉" => "2☴七",
                "甲戌" => "7☵二",
                "乙亥" => "3☲三",
                "丙子" => "6☶三",
                "丁丑" => "4☱七",
                "戊寅" => "8☳六",
                "己卯" => "7☵八",
                "庚辰" => "1☷九",
                "辛巳" => "3☲七",
                "壬午" => "2☴一",
                "癸未" => "4☱八",
                "甲申" => "3☲九",
                "乙酉" => "9☰四",
                "丙戌" => "6☶一",
                "丁亥" => "8☳八",
                "戊子" => "7☵四",
                "己丑" => "9☰二",
                "庚寅" => "3☲一",
                "辛卯" => "2☴三",
                "壬辰" => "6☶四",
                "癸巳" => "4☱六",
                "甲午" => "9☰一",
                "乙未" => "7☵六",
                "丙申" => "8☳四",
                "丁酉" => "4☱九",
                "戊戌" => "1☷六",
                "己亥" => "2☴二",
                "庚子" => "2☴九",
                "辛丑" => "1☷三",
                "壬寅" => "9☰七",
                "癸卯" => "8☳七",
                "甲辰" => "3☲二",
                "乙巳" => "7☵三",
                "丙午" => "4☱三",
                "丁未" => "6☶七",
                "戊申" => "2☴六",
                "己酉" => "3☲八",
                "庚戌" => "9☰九",
                "辛亥" => "7☵七",
                "壬子" => "8☳一",
                "癸丑" => "6☶八",
                "甲寅" => "7☵九",
                "乙卯" => "1☷四",
                "丙辰" => "4☱一",
                "丁巳" => "2☴八",
                "戊午" => "3☲四",
                "己未" => "1☷二",
                "庚申" => "7☵一",
                "辛酉" => "8☳三",
                "壬戌" => "4☱四",
                "癸亥" => "6☶六"
            ];
            echo "【玄空：" . $trigrams[$dayGanZhi] . "】";

            echo "【九星：" . $lunar->getDayNineStar()->getNumber() . $lunar->getDayNineStar()->getColor() . "】";
            echo "【宿：" . $lunar->getXiu() . "】";
            echo "【建除：" . $lunar->getZhiXing() . "】";

            // 每日宜忌
            $yiList = $lunar->getDayYi();
            $jiList = $lunar->getDayJi();

            $textColor = (in_array('諸事不宜', $yiList) || in_array('餘事勿取', $yiList) || in_array('諸事不宜', $jiList) || in_array('嫁娶', $jiList)) ? 'text-black' : 'text-danger';

            echo "<span class='$textColor'>【宜：" . implode("，", $yiList) . "】</span>";
            echo "【忌：" . implode("，", $jiList) . "】";
            echo "【沖：" . $lunar->getDayChongDesc() . "】【煞：" . $lunar->getDaySha() . "】";
            echo "【胎神：" . $lunar->getDayPositionTai() . "】";
            echo "【財神：" . $lunar->getDayPositionCaiDesc() . "】";
            echo "【喜神：" . $lunar->getPositionXiDesc() . "】";

            if (!empty($jsyq)) {
                echo "【吉神：" . implode("，", $jsyq) . "】";
            }

            if (!empty($xsyq)) {
                echo "【凶神：" . implode("，", $xsyq) . "】";
            }

			if ($sy . $sm . $sd === $td) {
                echo "<span class='click coll d-none' id='open{$day}'>【時辰吉凶宜忌：點我展開▼】</span><span class='click extend' id='close{$day}'>【時辰吉凶宜忌：點我收合▲】</span>";
                echo "";
            } else {
                echo "<span class='click coll' id='open{$day}'>【時辰吉凶宜忌：點我展開▼】</span><span class='click extend d-none' id='close{$day}'>【時辰吉凶宜忌：點我收合▲】</span>";
                echo "";
            }
			
            // 顯示詳細時辰信息
            if ($sy . $sm . $sd === $td) {
                echo "<span class='extend' id='detail{$day}'>";
            } else {
                echo "<span class='extend d-none' id='detail{$day}'>";
            }

            // 對應的時辰列表及其時間範圍
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

            foreach ($timePeriodList as $timePeriod => $hours) {
                $gregorianDate->setTime($hours[0], 0, 0);
                $lunarhour = Lunar::fromDate($gregorianDate);

                $timechong = $lunarhour->getTimeChongDesc();
                $timesha = $lunarhour->getTimeSha();
                $yiList = $lunarhour->getTimeYi();
                $jiList = $lunarhour->getTimeJi();

                echo "<br/>【" . ($lunarhour->getTimeTianShenLuck() === "吉" ? '<span class="text-danger">' : '') . $timePeriod . "時(" . sprintf('%02d', $hours[0]) . "-" . sprintf('%02d', $hours[1]) . ")" . $lunarhour->getTimeTianShenLuck();
                echo "◈天神：" . $lunarhour->getTimeTianShen() . "◈";
                echo "宜：" . implode('，', $yiList) . ($lunarhour->getTimeTianShenLuck() === "吉" ? '</span>' : '') . "◈";
                echo "忌：" . implode('，', $jiList) . "◈";
                echo "沖：" . $timechong . "◈";
                echo "煞：" . $timesha;
                echo "】";
            }

            echo "</span>";

            $JieQi = $lunar->getJieQi();

            if ($JieQi) {
                echo '<hr/><div class="row"><div class="col-md-12"><h3 class="float-left">【' . $JieQi . '】</h3>';
                $jieqidatetime = $lunar->getJieQiTable()[$JieQi]->toYmdHms();
                $jieqidatetime = substr($jieqidatetime, 0, 16);
                $formatted_jieqidatetime = date("Y年m月d日 H:i", strtotime($jieqidatetime));
                echo "【時間：" . $formatted_jieqidatetime . "】";

                $latitude = 23.6978;
                $longitude = 120.9605;
                $solardate = new DateTime();
                $solardate->setDate($sy, $szm, $szd);
                $sun_info = date_sun_info($solardate->getTimestamp(), $latitude, $longitude);
                $sunrise = date("H:i", $sun_info['sunrise']);
                $sunset = date("H:i", $sun_info['sunset']);
                echo "【日出：" . $sunrise . "】";
                echo "【日沒：" . $sunset . "】";

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

                echo '【太陽位於黃經' . $jieqi_info[$JieQi]['度數'] . '度】【' . $jieqi_info[$JieQi]['意義'] . '】</div></div>';
            }

            echo "</div>";
        }
        ?>
      </div>
    </div>
  </div>
<script>
<?php
$currentYear = date('Y');
$currentMonth = date('n');
$currentDay = date('j');

$shouldScroll = ($sy == $currentYear && $sm == $currentMonth);
?>	
document.addEventListener('DOMContentLoaded', () => {
    <?php if ($shouldScroll): ?>
        const targetElement = document.getElementById('<?php echo $currentDay; ?>');
        if (targetElement) {
            const previousSibling = targetElement.previousElementSibling;
            if (previousSibling && previousSibling.tagName === 'HR') {
                previousSibling.scrollIntoView();
            }
        }
    <?php endif; ?>

    document.querySelectorAll('[id^="open"]').forEach(element => {
        element.addEventListener('click', function() {
            toggleDetailVisibility(this.id.replace('open', ''));
        });
    });

    document.querySelectorAll('[id^="close"]').forEach(element => {
        element.addEventListener('click', function() {
            toggleDetailVisibility(this.id.replace('close', ''));
        });
    });

    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');

    function showNavButtons() {
        prevButton.style.display = 'block';
        nextButton.style.display = 'block';
    }

    function hideNavButtons() {
        setTimeout(() => {
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
        }, 2000);
    }

    window.addEventListener('scroll', () => {
        showNavButtons();
        hideNavButtons();
    });

    prevButton.addEventListener('click', () => changeMonth(-1));
    nextButton.addEventListener('click', () => changeMonth(1));

    document.addEventListener('keydown', event => {
        if (event.keyCode === 37) {
            changeMonth(-1);
        } else if (event.keyCode === 39) {
            changeMonth(1);
        }
    });

    const expandBtn = document.querySelector('#exall');
    const elementsToExpand = document.querySelectorAll('.extend');
    const elementsToCollapse = document.querySelectorAll('.coll');
	const elementsDay = document.querySelectorAll('.day');

    expandBtn.addEventListener('click', () => {
        elementsToExpand.forEach(element => element.classList.remove('d-none'));
        elementsToCollapse.forEach(element => element.classList.add('d-none'));
		elementsDay.forEach(element => element.classList.add('highlight'));
    });

    const pageTitle = document.getElementById("page-title");
    const navbarBrand = document.querySelector(".navbar-brand");

    window.addEventListener("scroll", () => {
        const pageTitleRect = pageTitle.getBoundingClientRect();
        if (pageTitleRect.top < 0 && pageTitleRect.bottom < 0) {
            if (!navbarBrand.querySelector(".scroll-title")) {
                const scrollTitle = document.createElement("span");
                scrollTitle.className = "scroll-title";
                scrollTitle.textContent = " " + pageTitle.textContent;
                navbarBrand.appendChild(scrollTitle);
            }
        } else if (pageTitleRect.top >= 0 && pageTitleRect.bottom >= 0) {
            const scrollTitle = navbarBrand.querySelector(".scroll-title");
            if (scrollTitle) {
                scrollTitle.remove();
            }
        }
    });
});

function toggleDetailVisibility(day) {
    const detailElement = document.getElementById('detail' + day);
    const openElement = document.getElementById('open' + day);
    const closeElement = document.getElementById('close' + day);

    if (detailElement.classList.contains('d-none')) {
        detailElement.classList.remove('d-none');
        detailElement.classList.add('fade-in');
        openElement.classList.add('d-none');
        closeElement.classList.remove('d-none');
        document.getElementById(day).classList.add('highlight');
    } else {
        detailElement.classList.add('d-none');
        openElement.classList.remove('d-none');
        closeElement.classList.add('d-none');
        document.getElementById(day).classList.remove('highlight');
    }
}

function changeMonth(offset) {
    const urlParams = new URLSearchParams(window.location.search);
    let currentYearMonth = urlParams.get('year-month');
    if (!currentYearMonth) {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1;
        currentYearMonth = `${currentYear}-${currentMonth.toString().padStart(2, '0')}`;
    }
    const currentDate = new Date(currentYearMonth);
    currentDate.setMonth(currentDate.getMonth() + offset);
    const newYear = currentDate.getFullYear();
    const newMonth = currentDate.getMonth() + 1;
    const newYearMonth = `${newYear}-${newMonth.toString().padStart(2, '0')}`;
    window.location.href = `?year-month=${newYearMonth}`;
}
</script>

<?php include 'footer.php'; ?>	
</body>	
</html>
