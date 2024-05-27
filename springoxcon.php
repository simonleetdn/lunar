<?php
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
            '金' => '在耳前',
            '木' => '在耳後',
            '水' => '左在耳前，右在耳後',
            '火' => '右在耳前，左在耳後',
            '土' => '在頂真上'
        ];

        // 芒神耳朵蓋法和帽子
        $earCover = [
            '子' => '罨耳全戴',
            '丑' => '罨耳全戴',
            '寅' => '罨耳全戴，揭起左邊',
            '卯' => '罨耳用右手提',
            '辰' => '罨耳用左手提',
            '巳' => '罨耳用右手提',
            '午' => '罨耳用左手提',
            '未' => '罨耳用右手提',
            '申' => '罨耳用左手提',
            '酉' => '罨耳用右手提',
            '戌' => '罨耳用左手提',
            '亥' => '罨耳全戴，揭起右邊'
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

// 定义地支到芒神位置的映射
$yearZhiToMangShenPosition = [
    '子' => '左', // 阳年
    '丑' => '右', // 阴年
    '寅' => '左', // 阳年
    '卯' => '右', // 阴年
    '辰' => '左', // 阳年
    '巳' => '右', // 阴年
    '午' => '左', // 阳年
    '未' => '右', // 阴年
    '申' => '左', // 阳年
    '酉' => '右', // 阴年
    '戌' => '左', // 阳年
    '亥' => '右'  // 阴年
];

// 獲取年份的天干、地支、納音等資訊
$yearGan = $lunar->getYearGan();
$yearZhi = $lunar->getYearZhi();
$yearNaYin = $lunar->getYearNaYin();
$lichun = $lunar->getJieQiTable()['立春'];
$lichunLunar = $lichun->getLunar();

// 获取立春的具体日期和时间
$liChunYear = $lichun->getYear();
$liChunMonth = $lichun->getMonth();
$liChunDay = $lichun->getDay();
$liChunHour = $lichun->getHour();
$liChunMinute = $lichun->getMinute();
$liChunSecond = $lichun->getSecond();
if ($liChunHour == 23) {
    $lichunLunar = $lichunLunar->next(1);
}
$liChunDayGan = $lichunLunar->getDayGan();
$liChunDayZhi = $lichunLunar->getDayZhi();
$liChunDayNaYin = mb_substr($lichunLunar->getDayNaYin(), 2, 1);
$hourZhi = $lichunLunar->getTimeZhi();
// 创建立春的 DateTime 对象
$liChunDateTime = new DateTime("$liChunYear-$liChunMonth-$liChunDay $liChunHour:$liChunMinute:$liChunSecond");

        // 設定流年屬陽的天干
        $yangGan = ['甲', '丙', '戊', '庚', '壬'];

        // 判斷流年屬陰還是屬陽
        $isYangYear = in_array($yearGan, $yangGan);

        // 根據流年屬陰陽來確定牛尾擺向、牛口開合和牛踏板用門的左或右扇
        $tailDirection = $isYangYear ? '左繳' : '右繳';
        $mouthState = $isYangYear ? '開' : '合';
        $doorSide = $isYangYear ? '左扇' : '右扇';

// 获取春节的具体日期
$chunJieYear = $solar->getYear();
$chunJieMonth = $solar->getMonth();
$chunJieDay = $solar->getDay();

// 创建春节的 DateTime 对象
$springFestivalDateTime = new DateTime("$chunJieYear-$chunJieMonth-$chunJieDay 00:00:00");

// 计算立春距正月元旦的时间差，以秒为单位
$interval = $springFestivalDateTime->diff($liChunDateTime);
$secondsDifference = ($liChunDateTime->getTimestamp() - $springFestivalDateTime->getTimestamp());
// 获取天数差
$daysDifference = floor($secondsDifference / (24 * 60 * 60));
// 确定芒神的位置

// 定义地支到芒神位置的映射
$yearZhiToMangShenPosition = [
    '子' => '左', // 阳年
    '丑' => '右', // 阴年
    '寅' => '左', // 阳年
    '卯' => '右', // 阴年
    '辰' => '左', // 阳年
    '巳' => '右', // 阴年
    '午' => '左', // 阳年
    '未' => '右', // 阴年
    '申' => '左', // 阳年
    '酉' => '右', // 阴年
    '戌' => '左', // 阳年
    '亥' => '右'  // 阴年
];

if ($daysDifference > -5 && $daysDifference < 5) {
    $mangShenPosition = "芒神忙與牛並立於牛".$yearZhiToMangShenPosition[$yearZhi]."邊";
} elseif ($daysDifference < -5) {
    $mangShenPosition = "芒神早忙，立於牛".$yearZhiToMangShenPosition[$yearZhi]."前邊";
} else {
    $mangShenPosition = "芒神晚閑，立於牛".$yearZhiToMangShenPosition[$yearZhi]."後邊";
}


// 準備字串內容
$oxcontent = "春牛身高四尺，長八尺，尾長一尺二寸，牛頭{$headColors[$yearGan]}，";
$oxcontent .= "牛身{$bodyColors[$yearZhi]}，";
$oxcontent .= "牛腹{$bellyColors[mb_substr($lunar->getYearNaYin(), 2, 1)]}，";
$oxcontent .= "牛角、耳、尾{$tailColors[$liChunDayGan]}，";
$oxcontent .= "牛脛{$footColors[$liChunDayZhi]}，";
$oxcontent .= "牛蹄{$tiColors[$liChunDayNaYin]}，";
$oxcontent .= "牛尾{$tailDirection}。";
$oxcontent .= "牛口{$mouthState}，牛籠頭拘繩桑柘木，";
$oxcontent .= "用{$ropeMaterials[$liChunDayZhi]}結{$ropeColors[$liChunDayGan]}，";
$oxcontent .= "牛踏板縣門{$doorSide}。芒神身高三尺六寸五分，";
$oxcontent .= "面如{$mangShenImage[$yearZhi]}像，";
$oxcontent .= "{$mangShenClothes[$liChunDayZhi]['衣服']}繫{$mangShenClothes[$liChunDayZhi]['腰帶']}，";
$oxcontent .= "平梳兩髻{$mangShenHair[$liChunDayNaYin]}，{$earCover[$hourZhi]}，{$shoePantsSettings[$liChunDayNaYin]}，";
$oxcontent .= "鞭杖用柳枝，長二尺四寸，五色醮染用{$ribbonKnot[$liChunDayZhi]}，";
$oxcontent .= "{$mangShenPosition}。";

?>
