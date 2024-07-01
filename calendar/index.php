<?php
date_default_timezone_set('Asia/Taipei');
require '../Lunar.php';
    use com\nlf\calendar\Lunar;
    use com\nlf\calendar\Solar;
	use com\nlf\calendar\LunarMonth;
function updateCalendar() {
    $date = new DateTime();
    $solar = Solar::fromDate($date);
    $lunar = $solar->getLunar();
    
    $lunarYear = $lunar->getYear();
    $lunarMonth = $lunar->getMonth();
    $solarYear = $solar->getYear();
    $solarMonth = $solar->getMonth();
    $solarDay = $solar->getDay();
    $solarWeek = $solar->getWeekInChinese();
	$lunarMonthObj = LunarMonth::fromYm($lunarYear, $lunarMonth);

    $yearStemBranch = $lunar->getYearInGanZhi() . $lunar->getYearShengXiao() . '年';
    $lunarDate = $lunar->getMonthInChinese() . '月' . $lunar->getDayInChinese() . '日';
    $lunarMonthSize = $lunarMonthObj->getDayCount() > 29 ? '大' : '小';
    $lunarBig = '農' . $lunarMonthSize;
    $luckyActivities = '宜：' . implode('，', $lunar->getDayYi()) . '。';
    $lunarWeek = '星期' . $solarWeek;

    $luckyActivitiesWrapped = wordwrap($luckyActivities, 45, "\n", true);
    $lines = explode("\n", $luckyActivitiesWrapped);

    ob_start();
?>
<!DOCTYPE html>
<html lang="zh-TW">
	<head>
		<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EM79Q5QNEK"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-EM79Q5QNEK');
</script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="農民曆, 黃曆, 農曆, 通勝, 每月農民曆, 宜日速查, 秤骨算命, 春牛圖, 地母經, 日曆">
	<meta name="author" content="Simon Lee">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="農民曆查詢 | 精確、可靠的農曆日期資訊">
    <meta property="og:description" content="在這裡查詢每個月的農曆日期！我們提供精確的農民曆資訊，幫助您掌握日常生活和重要節日的日期。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://lunar.ioi.tw/">
    <meta property="og:image" content="https://lunar.ioi.tw/farmer.jpg">
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "WebSite",
        "name": "農民曆",
        "url": "https://lunar.ioi.tw/",
        "description": "在這裡查詢每個月的農曆日期！我們提供精確的農民曆資訊，幫助您掌握日常生活和重要節日的日期。",
        "image": "https://lunar.ioi.tw/farmer.jpg"
    }
    </script>
<meta name="description" content="自動切換顯示每天日曆，可以當作電子日曆使用。">
<link rel="canonical" href="https://lunar.ioi.tw/calendar/" />
  <title>日曆</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;700&family=Noto+Serif+TC:wght@400;700&display=swap');

        .st0 { fill: none; }
        .st1 { fill: #1D2088; }
<?php
	$fillColor = "#1D2088"; 
if ($solarWeek === "六" || $solarWeek === "日") {
    $fillColor = "#D70000";
}
echo ".st1 { fill: $fillColor; }";
?>
        
        /* 合并Noto Serif TC字体样式 */
        .noto-serif {
            font-family: "Noto Serif TC", serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
        
        /* 合并Noto Sans TC字体样式 */
        .noto-sans {
            font-family: "Noto Sans TC", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
        
        /* 特定样式 */
        .st2 { font-weight: 700; font-size: 13.0467px; }
        .st4 { fill: #F7F8F8; }
        .st5 { font-weight: 400; font-size: 13.0467px; }
        .st6 { font-weight: 700; font-size: 31.5295px; glyph-orientation-vertical: 0; writing-mode: tb; }
        .st7 { font-size: 31.5295px; glyph-orientation-vertical: 0; writing-mode: tb; }
        .st8 { font-size: 130.467px; }
        .st9 { font-size: 15.2211px; }
        .st10 { font-size: 31.5295px; }
        .st12 { font-weight: 700; font-size: 9.785px; }
        .styi { font-weight: 400; font-size: 8px; }
        .st13 { writing-mode: tb; }
        .st14 { font-weight: 700; font-size: 7px; glyph-orientation-vertical: 0; }
        .st16 { font-size: 29px; }
        .st17 { letter-spacing: 5; }
    </style>
</head>
<body>
    <svg version="1.1" id="calendar" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 306.14 285.04" style="enable-background:new 0 0 306.14 378.04;" xml:space="preserve">
        <rect x="5.67" y="11.89" class="st0" width="60.14" height="23.63"/>
        <text id="yearStemBranch" transform="matrix(1 0 0 1 9.6449 23.3745)" class="st1 st2 st3 noto-serif"><?= $yearStemBranch ?></text>
        <path class="st1" d="M47.99,57.04H18.67c-2.95,0-5.36-2.41-5.36-5.36v-9.49c0-2.95,2.41-5.36,5.36-5.36h29.32
            c2.95,0,5.36,2.41,5.36,5.36v9.49C53.35,54.63,50.94,57.04,47.99,57.04z"/>
        <rect x="13.31" y="40.25" class="st0" width="40.05" height="16.79"/>
        <text id="lunarBig" transform="matrix(1 0 0 1 20.2853 51.7332)" class="st4 st5 st3 noto-serif"><?= $lunarBig ?></text>
        <rect x="16.25" y="61.99" class="st0" width="34.16" height="165.74"/>
        <text id="lunarDate" transform="matrix(1 0 0 0.9 34.6474 61.9946)" class="st1 st6 st7 noto-serif"><?= $lunarDate ?></text>
        <rect x="46.16" y="89.13" class="st0" width="224.13" height="202.22"/>
        <text id="solarDate" transform="matrix(<?= $solarDay < 10 ? '2 0 0 2 78.9026' : '1.07 0 0 2 73.3499' ?> 203.9411)" class="st1 st2 st8 noto-sans"><?= $solarDay ?></text>    
        <rect x="247.33" y="14.88" class="st0" width="55.41" height="19.97"/>
        <text id="solarYear" transform="matrix(1.21 0 0 1 252.6406 28.272)" class="st1 st2 st9 noto-sans"><?= $solarYear ?></text>
        <rect x="233.54" y="43.59" class="st0" width="47.5" height="39.59"/>
        <text id="solarMonth" transform="matrix(<?= $solarMonth < 10 ? '1.21 0 0 1.33 257.848' : '0.8 0 0 1.33 251.7399' ?> 71.3362)" class="st1 st2 st10 noto-sans"><?= $solarMonth ?></text>
        <rect x="284.58" y="63.11" class="st0" width="16.75" height="18.68"/>
        <text transform="matrix(1.21 0 0 1.33 284.5765 71.7229)" class="st1 st12 noto-sans">月</text>
        <rect x="147.19" y="86.05" class="st0" width="145.76" height="123.63"/>
        <text id="luckyActivities" transform="matrix(1.07 0 0 1 300.4529 86.0481)" class="st13 styi st1 noto-serif">
            <?php foreach ($lines as $line): ?>
                <tspan y="0" dx="-1.2em"><?= htmlspecialchars($line) ?></tspan>
            <?php endforeach; ?>
        </text>
        <rect y="228.76" class="st0" width="306.14" height="48"/>
        <text id="lunarWeek" transform="matrix(1 0 0 1 103.771 254.281)" class="st1 st2 st16 st17 noto-sans"><?= $lunarWeek ?></text>
    </svg>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2428072621366113" crossorigin="anonymous"></script>
<!-- 日曆 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-2428072621366113"
     data-ad-slot="8478025043"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
    <?php
    $output = ob_get_clean();
    return $output;
}

echo updateCalendar();
?>
    <script>
        // Function to calculate milliseconds until midnight in Taiwan time
        function getMillisecondsUntilMidnight() {
            var now = new Date();
            var taiwanOffset = 8 * 60; // Taiwan is UTC+8
            var localOffset = now.getTimezoneOffset(); // Local time zone offset in minutes
            var taiwanTime = new Date(now.getTime() + (taiwanOffset + localOffset) * 60 * 1000);
            
            var midnight = new Date(taiwanTime);
            midnight.setHours(24, 0, 0, 0); // Set to midnight

            return midnight - taiwanTime;
        }

        // Set a timeout to reload the page at midnight Taiwan time
        setTimeout(function() {
            location.reload();
        }, getMillisecondsUntilMidnight());

        // Optional: Also reload the page every 24 hours to ensure it reloads at midnight in case the page was loaded after midnight
        setInterval(function() {
            location.reload();
        }, 24 * 60 * 60 * 1000);
    </script>
</body>
</html>
