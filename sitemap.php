<?php
// 設置header，確保內容是XML格式
header('Content-Type: application/xml; charset=utf-8');

// 開啟緩衝區輸出
ob_start();

// 輸出XML聲明和URL集
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">

  <url>
    <loc>https://lunar.ioi.tw/</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>

  <url>
    <loc>https://lunar.ioi.tw/lunardate.php</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.9</priority>
  </url>
	
  <url>
    <loc>https://lunar.ioi.tw/yidate.php</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.9</priority>
  </url>
	
  <url>
    <loc>https://lunar.ioi.tw/yuangang.php</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.9</priority>
  </url>
	
  <url>
    <loc>https://lunar.ioi.tw/ziwei.php</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.9</priority>
  </url>

  <url>
    <loc>https://lunar.ioi.tw/calendar/</loc>
    <lastmod>2024-06-03</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
	
  <url>
    <loc>https://lunar.ioi.tw/springox.php</loc>
    <lastmod>2024-05-27</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.9</priority>
  </url>	
<?php
$currentYear = date('Y');
$years = [
    $currentYear - 1,
    $currentYear,
    $currentYear + 1
];

foreach ($years as $year) {
    for ($month = 1; $month <= 12; $month++) {
        $month_padded = str_pad($month, 2, '0', STR_PAD_LEFT); // 確保月份是兩位數
        echo "<url>\n";
        echo "  <loc>https://lunar.ioi.tw/lunardate.php?year-month=$year-$month_padded</loc>\n";
        echo "  <lastmod>2024-06-26</lastmod>\n";
        echo "  <changefreq>monthly</changefreq>\n";
        echo "  <priority>0.9</priority>\n";
        echo "</url>\n";
    }
}

$activities = ['祭祀', '祈福', '求嗣', '開光', '塑繪', '齋醮', '沐浴', '酬神', '造廟', '謝土', '出火', '雕刻','嫁娶', '納采', '納婿', '歸寧', '安床', '合帳', '冠笄', '訂盟', '進人口','裁衣', '理髮', '整手足甲', '針灸', '求醫', '治病', '出行', '移徙', '會親友', '習藝', '入學','修墳', '啟鉆', '破土', '安葬', '立碑', '成服', '除服', '開生墳', '合壽木', '入殮', '移柩', '普渡','入宅', '安香', '安門', '修造', '起基', '動土', '上梁', '豎柱', '合脊', '造倉', '作竈', '解除','拆卸', '破屋', '壞垣', '補垣', '伐木', '開柱眼', '開廁', '造倉', '塞穴', '平治道塗', '造橋', '築堤', '開池', '開渠', '掘井', '掃舍', '放水', '造畜稠', '修門', '定磉', '作梁', '修飾垣墻','架馬', '開市', '掛匾', '納財', '開倉', '置產', '雇庸', '出貨財', '安機械', '造車器', '經絡', '造船', '割蜜', '栽種', '取漁', '結網', '牧養', '安碓磑', '赴任', '立券', '交易','塑繪', '雕刻', '習藝', '裁衣', '納畜', '捕捉', '畋獵', '教牛馬', '斷蟻'];
foreach ($years as $year) {
    foreach ($activities as $activity) {
        $encoded_activity = urlencode($activity); // URL編碼活動名稱，確保其安全性
        echo "<url>\n";
        echo "  <loc>https://lunar.ioi.tw/yidate.php?year-yi=$year-$encoded_activity</loc>\n";
        echo "  <lastmod>2024-06-26</lastmod>\n";
        echo "  <changefreq>monthly</changefreq>\n";
        echo "  <priority>0.8</priority>\n";
        echo "</url>\n";
    }
}

$startYear = $currentYear - 20;
$endYear = $currentYear + 20;

for ($year = $startYear; $year <= $endYear; $year++) {
    echo "<url>\n";
    echo "  <loc>https://lunar.ioi.tw/springox.php?year=$year</loc>\n";
    echo "  <lastmod>2024-05-27</lastmod>\n"; // 使用指定日期作為最後修改日期
    echo "  <changefreq>yearly</changefreq>\n";
    echo "  <priority>0.8</priority>\n";
    echo "</url>\n";
}
?>
</urlset>
<?php
// 獲取緩衝區的內容
$xmlContent = ob_get_clean();

// 將內容寫入sitemap.xml文件
file_put_contents('sitemap.xml', $xmlContent);

// 顯示生成的XML內容
echo $xmlContent;
?>
