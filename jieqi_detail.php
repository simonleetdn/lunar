<?php
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

$jieqiSolar = isset($jieqiSolar) ? $jieqiSolar : null;
if ($jieqiSolar === null) {
    $jieqiTable = $lunar->getJieQiTable();
    foreach ($jieqiTable as $name => $solarDate) {
        if (convertJieQiName($name) === $JieQi) {
            $jieqiSolar = $solarDate;
            break;
        }
    }
}

if ($jieqiSolar) {
    $jieqidatetime = substr($jieqiSolar->toYmdHms(), 0, 16);
    $formatted_jieqidatetime = date("Y年m月d日 H:i", strtotime($jieqidatetime));
    echo "【時間：" . $formatted_jieqidatetime . "】";

    $latitude = 23.6978;
    $longitude = 120.9605;
    $sun_info = date_sun_info(strtotime($jieqiSolar->toYmd()), $latitude, $longitude);
    $sunrise = date("H:i", $sun_info['sunrise']);
    $sunset = date("H:i", $sun_info['sunset']);
    echo "【日出：" . $sunrise . "】";
    echo "【日沒：" . $sunset . "】";

    if (isset($jieqi_info[$JieQi])) {
        echo '【太陽位於黃經' . $jieqi_info[$JieQi]['度數'] . '度】【' . $jieqi_info[$JieQi]['意義'] . '】';
    }
}