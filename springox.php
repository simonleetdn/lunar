<?php
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

// 預設顯示今年，若有輸入年份則依輸入為主
$year = (isset($_GET['year']) && $_GET['year'] !== '') ? (int)$_GET['year'] : (int)date('Y');

// 初始化為農曆正月初一
$lunar = Lunar::fromYmd($year, 1, 1);
$solar = $lunar->getSolar();
$ganZhiYear = $lunar->getYearInGanZhi(); // 取得該年干支（如：丙午）

include_once("springoxcon.php");

/**
 * 自動解析 $oxcontent 歌訣文字為傳統春牛圖繪製參數（支援繁簡體字）
 */
function parseSpringOx($text) {
    $colorMap = [
        '青' => '#2B6E4B', // 傳統石青/松綠
        '綠' => '#2B6E4B',
        '绿' => '#2B6E4B',
        '紅' => '#C83228', // 傳統朱砂紅
        '红' => '#C83228',
        '赤' => '#C83228',
        '黃' => '#E5A922', // 傳統藤黃
        '黄' => '#E5A922',
        '白' => '#F4EFE4', // 傳統胡粉白
        '黑' => '#2B2927', // 傳統松煙墨黑
        '皂' => '#2B2927'
    ];

    $getColor = function($pattern, $default = '#C83228') use ($text, $colorMap) {
        if (preg_match($pattern, $text, $matches)) {
            $c = $matches[1];
            return $colorMap[$c] ?? $default;
        }
        return $default;
    };

    // 1. 解析春牛顏色與狀態
    $oxHead  = $getColor('/牛頭([青綠绿紅红赤黃黄白黑皂])/u', '#C83228');
    $oxBody  = $getColor('/牛身([青綠绿紅红赤黃黄白黑皂])/u', '#C83228');
    $oxBelly = $getColor('/牛腹([青綠绿紅红赤黃黄白黑皂])/u', '#2B2927');
    $oxHorn  = $getColor('/牛角[、,，]?耳[、,，]?尾([青綠绿紅红赤黃黄白黑皂])/u', '#E5A922');
    $oxLeg   = $getColor('/牛脛([青綠绿紅红赤黃黄白黑皂])/u', '#F4EFE4');
    $oxHoof  = $getColor('/牛蹄([青綠绿紅红赤黃黄白黑皂])/u', '#E5A922');
    $oxRope  = $getColor('/苧繩結([青綠绿紅红赤黃黄白黑皂])/u', '#2B6E4B');

    $mouthOpen = (mb_strpos($text, '牛口開') !== false || mb_strpos($text, '牛口开') !== false);
    $tailDir   = (mb_strpos($text, '牛尾右繳') !== false || mb_strpos($text, '牛尾右缴') !== false) ? 'right' : 'left';

    // 2. 解析芒神服色、髮飾、鞋履與站位
    $msClothes = $getColor('/([青綠绿紅红赤黃黄白黑皂])衣/u', '#C83228');
    $msBelt    = $getColor('/繫([青綠绿紅红赤黃黄白黑皂])腰帶/u', '#2B2927');

    // 罨耳戴法
    $earCover = 'full';
    if (mb_strpos($text, '揭起左') !== false) {
        $earCover = 'lift_left';
    } elseif (mb_strpos($text, '揭起右') !== false) {
        $earCover = 'lift_right';
    } elseif (mb_strpos($text, '不戴') !== false || mb_strpos($text, '無罨耳') !== false) {
        $earCover = 'none';
    }

    // 是否有行纏鞋子
    $hasShoes = (
        mb_strpos($text, '無行纏鞋子') === false &&
        mb_strpos($text, '无行缠鞋子') === false &&
        mb_strpos($text, '無鞋') === false &&
        mb_strpos($text, '赤腳') === false
    );

    // 芒神站位（牛前 / 並肩 / 牛後）
    $posX = 95; // 預設牛左前邊（早忙）
    if (mb_strpos($text, '牛後') !== false || mb_strpos($text, '牛后') !== false || mb_strpos($text, '晚閒') !== false || mb_strpos($text, '晚闲') !== false) {
        $posX = 505; // 站牛後
    } elseif (mb_strpos($text, '並肩') !== false || mb_strpos($text, '并肩') !== false || mb_strpos($text, '平立') !== false || mb_strpos($text, '牛中') !== false) {
        $posX = 300; // 站牛身旁
    }

    // 芒神年齡相貌
    $age = 'young';
    if (mb_strpos($text, '老人') !== false) {
        $age = 'old';
    } elseif (mb_strpos($text, '孩童') !== false || mb_strpos($text, '小兒') !== false || mb_strpos($text, '小儿') !== false) {
        $age = 'child';
    }

    return [
        'ox' => [
            'head' => $oxHead,
            'body' => $oxBody,
            'belly' => $oxBelly,
            'hornEarTail' => $oxHorn,
            'leg' => $oxLeg,
            'hoof' => $oxHoof,
            'rope' => $oxRope,
            'mouthOpen' => $mouthOpen,
            'tailDir' => $tailDir
        ],
        'mangshen' => [
            'clothes' => $msClothes,
            'belt' => $msBelt,
            'earCover' => $earCover,
            'hasShoes' => $hasShoes,
            'posX' => $posX,
            'age' => $age
        ]
    ];
}

$oxVisualData = parseSpringOx($oxcontent ?? '');
?>
<?php include 'metaseo.php'; ?>
<meta name="description" content="探索不同年份的春牛芒神服色及其象徵意義，了解每一年圖中描繪的豐收預兆和文化內涵，感受中國傳統藝術的魅力。">
<title><?php echo htmlspecialchars($year) . "年"; ?>春牛芒神服色</title>
<?php include 'head.php'; ?>
<style>
  /* 僅針對春牛圖區塊設定樣式，不影響全站導覽列與容器版面 */
  .springox-almanac-card {
    background-color: #FAF3E3;
    border: 2px solid #9E2A22;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(60, 35, 15, 0.12);
  }
  .springox-svg-frame {
    width: 100%;
    overflow: hidden;
    background: #F6ECD6;
    border-radius: 4px;
  }
  .springox-svg-frame svg {
    display: block;
    width: 100%;
    height: auto;
  }
  .springox-verse-box {
    background-color: #F2E6CE;
    border: 1px solid #D3BFA0;
    border-left: 5px solid #9E2A22;
    font-family: "Noto Serif TC", "PMingLiU", "MingLiU", serif;
    font-size: 1.08rem;
    line-height: 1.9;
    color: #2B1B12;
  }
  .springox-legend-item {
    display: inline-flex;
    align-items: center;
    background: #FFFDF8;
    border: 1px solid #D8C7AC;
    border-radius: 4px;
    padding: 3px 9px;
    margin: 0 6px 6px 0;
    font-size: 0.88rem;
    color: #3E2A1E;
  }
  .springox-color-dot {
    display: inline-block;
    width: 13px;
    height: 13px;
    border-radius: 2px;
    border: 1px solid #4A3525;
    margin-left: 5px;
  }
</style>
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
            <a id="lunar" class="nav-link" href="lunardate.php">每月農民曆</a>
          </li>
          <li class="nav-item">
            <a id="yidate" class="nav-link" href="yidate.php">每年宜日速查</a>
          </li>
          <li class="nav-item">
            <a id="jieqi" class="nav-link" href="jieqi.php">節氣速查</a>
          </li>
          <li class="nav-item">
            <a id="calendar" class="nav-link" href="/calendar/">日曆(自動更新)</a>
          </li>
          <li class="nav-item">
            <a id="yuangang" class="nav-link" href="yuangang.php?birthDateTime=<?php echo date('Y-m-d\TH:i'); ?>">稱骨算命</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <form class="form-inline ml-auto" id="year-form" method="get">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">選擇年份</span>
            </div>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>" class="form-control" style="width: 95px;">
            <div class="input-group-append">
              <button type="submit" class="btn btn-warning">進呈</button>
            </div>
          </div>
        </form>
        <hr/>
        <h2 class="mb-3">【<?php echo htmlspecialchars($year) . "年（" . htmlspecialchars($ganZhiYear) . "）"; ?>春牛芒神服色】</h2>

        <!-- 傳統版畫風格春牛圖卡片 -->
        <div class="card springox-almanac-card mb-4">
          <div class="card-body p-3 p-md-4">
            <div class="row align-items-center">
              
              <!-- 左側：傳統通書版畫 SVG -->
              <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="springox-svg-frame">
                  <svg id="spring-ox-svg" viewBox="0 0 680 420" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                      <!-- 古籍宣紙底色漸層 -->
                      <radialGradient id="xuan-paper" cx="50%" cy="45%" r="65%">
                        <stop offset="0%" stop-color="#FBF5E6"/>
                        <stop offset="70%" stop-color="#F3E5C8"/>
                        <stop offset="100%" stop-color="#E5D1AA"/>
                      </radialGradient>
                      <!-- 遠山淡墨漸層 -->
                      <linearGradient id="hill-ink" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#D5C3A1" stop-opacity="0.55"/>
                        <stop offset="100%" stop-color="#EFE0C2" stop-opacity="0"/>
                      </linearGradient>
                    </defs>

                    <!-- 背景紙張 -->
                    <rect x="0" y="0" width="680" height="420" fill="url(#xuan-paper)"/>

                    <!-- 傳統遠山與祥雲 -->
                    <path d="M 16 260 Q 110 195 220 245 Q 330 185 450 240 Q 560 190 664 250 L 664 345 L 16 345 Z" fill="url(#hill-ink)"/>
                    <!-- 左上祥雲 -->
                    <g fill="none" stroke="#B89F78" stroke-width="1.8" opacity="0.75">
                      <path d="M 48 65 C 62 52, 85 52, 95 65 C 108 58, 128 62, 134 74 C 115 78, 82 78, 52 75 Z"/>
                      <path d="M 72 82 C 92 78, 125 78, 152 84"/>
                    </g>
                    <!-- 中上祥雲 -->
                    <g fill="none" stroke="#B89F78" stroke-width="1.6" opacity="0.65">
                      <path d="M 330 48 C 345 36, 368 38, 378 50 C 392 44, 412 48, 418 58 C 395 62, 360 62, 332 58 Z"/>
                    </g>

                    <!-- 大地與草坡 -->
                    <rect x="16" y="344" width="648" height="60" fill="#E6D2AE"/>
                    <path d="M 16 344 Q 180 338 340 345 Q 500 352 664 343" stroke="#6E553A" stroke-width="2" fill="none"/>
                    <!-- 傳統水墨草叢 -->
                    <g stroke="#4E5B31" stroke-width="1.8" fill="none" stroke-linecap="round">
                      <path d="M 65 352 L 58 338 M 68 352 L 69 335 M 72 352 L 80 340"/>
                      <path d="M 275 358 L 268 345 M 279 358 L 281 342 M 283 358 L 291 346"/>
                      <path d="M 555 354 L 548 340 M 559 354 L 560 337 M 563 354 L 572 342"/>
                    </g>

                    <!-- 右上角：傳統通書直書牌匾與朱文印章 -->
                    <g transform="translate(598, 30)">
                      <rect x="0" y="0" width="48" height="156" fill="#FAF2DF" stroke="#9E2A22" stroke-width="2"/>
                      <rect x="3" y="3" width="42" height="150" fill="none" stroke="#9E2A22" stroke-width="0.8"/>
                      <text x="24" y="26" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12"><?php echo mb_substr($ganZhiYear, 0, 1); ?></text>
                      <text x="24" y="50" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12"><?php echo mb_substr($ganZhiYear, 1, 1); ?></text>
                      <text x="24" y="74" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12">年</text>
                      <text x="24" y="98" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">春</text>
                      <text x="24" y="122" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">牛</text>
                      <text x="24" y="146" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">圖</text>
                      <!-- 朱文方印 -->
                      <rect x="10" y="166" width="28" height="28" rx="2" fill="#B82921"/>
                      <text x="24" y="179" text-anchor="middle" font-family="'Noto Serif TC', serif" font-size="10" font-weight="bold" fill="#FAF2DF">迎春</text>
                      <text x="24" y="190" text-anchor="middle" font-family="'Noto Serif TC', serif" font-size="10" font-weight="bold" fill="#FAF2DF">大吉</text>
                    </g>

                    <!-- ================= 傳統版畫春牛圖層 (Ox) ================= -->
                    <g id="ox-group" transform="translate(230, 128)">
                      <!-- 牛尾（左繳／右繳切換） -->
                      <g id="ox-tail-left">
                        <path id="ox-tail-left-path" d="M 244 76 C 266 94, 256 144, 228 160" stroke="#E5A922" stroke-width="7" fill="none" stroke-linecap="round"/>
                        <path d="M 244 76 C 266 94, 256 144, 228 160" stroke="#2B1B12" stroke-width="2" fill="none" stroke-dasharray="140 0"/>
                        <!-- 尾毛刷紋 -->
                        <path id="ox-tail-left-tuft" d="M 228 160 C 218 166, 214 176, 222 182 C 230 178, 236 170, 234 160 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2"/>
                      </g>
                      <g id="ox-tail-right" style="display:none;">
                        <path id="ox-tail-right-path" d="M 244 76 C 278 86, 286 135, 268 165" stroke="#E5A922" stroke-width="7" fill="none" stroke-linecap="round"/>
                        <path d="M 244 76 C 278 86, 286 135, 268 165" stroke="#2B1B12" stroke-width="2" fill="none"/>
                        <path id="ox-tail-right-tuft" d="M 268 165 C 262 174, 266 184, 275 182 C 280 175, 278 166, 272 162 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2"/>
                      </g>

                      <!-- 遠側雙腿（牛脛） -->
                      <path id="ox-leg-far-1" d="M 68 130 L 62 172 L 66 206 L 82 206 L 80 172 L 86 132 Z" fill="#F4EFE4" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>
                      <path id="ox-leg-far-2" d="M 192 132 L 198 170 L 190 206 L 206 206 L 214 170 L 210 132 Z" fill="#F4EFE4" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>
                      <!-- 遠側牛蹄 -->
                      <path id="ox-hoof-far-1" d="M 65 206 L 61 218 L 83 218 L 82 206 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>
                      <path id="ox-hoof-far-2" d="M 189 206 L 185 218 L 207 218 L 206 206 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>

                      <!-- 牛身（含傳統肩峰與厚實軀幹） -->
                      <path id="ox-body" d="M 54 68 C 72 38, 112 34, 146 52 C 178 62, 222 54, 246 76 C 258 94, 254 128, 234 144 C 208 158, 165 160, 120 158 C 84 156, 56 144, 48 118 Z" fill="#C83228" stroke="#2B1B12" stroke-width="2.8" stroke-linejoin="round"/>

                      <!-- 牛腹（弧形分色區） -->
                      <path id="ox-belly" d="M 78 122 C 118 134, 174 134, 214 120 C 204 148, 176 159, 145 159 C 112 159, 86 147, 78 122 Z" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>

                      <!-- 版畫肌理線（肋骨與肩胛墨線） -->
                      <g stroke="#2B1B12" stroke-width="1.6" fill="none" opacity="0.45" stroke-linecap="round">
                        <path d="M 105 64 C 98 82, 98 102, 106 116"/>
                        <path d="M 136 74 C 132 88, 132 102, 136 112"/>
                        <path d="M 150 76 C 146 90, 146 104, 150 114"/>
                        <path d="M 164 76 C 160 90, 160 104, 164 114"/>
                        <path d="M 216 74 C 206 90, 206 110, 216 124"/>
                      </g>

                      <!-- 近側雙腿（牛脛） -->
                      <path id="ox-leg-near-1" d="M 88 128 L 82 174 L 86 212 L 103 212 L 101 174 L 108 130 Z" fill="#F4EFE4" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
                      <path id="ox-leg-near-2" d="M 212 126 L 222 170 L 214 212 L 231 212 L 239 170 L 232 126 Z" fill="#F4EFE4" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
                      <!-- 近側牛蹄（偶蹄開叉細節） -->
                      <path id="ox-hoof-near-1" d="M 85 212 L 80 225 L 93 225 L 95 219 L 97 225 L 105 225 L 103 212 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>
                      <path id="ox-hoof-near-2" d="M 213 212 L 208 225 L 221 225 L 223 219 L 225 225 L 233 225 L 231 212 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>

                      <!-- 遠側牛角 -->
                      <path id="ox-horn-far" d="M 38 52 C 26 32, 28 14, 42 8 C 36 22, 38 36, 48 48 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.3" stroke-linejoin="round"/>

                      <!-- 牛頭（傳統水牛／黃牛造型） -->
                      <path id="ox-head" d="M 46 46 C 62 46, 74 58, 72 78 C 70 98, 56 112, 36 114 C 18 116, 4 108, 2 96 C 0 84, 12 72, 24 62 C 32 54, 38 46, 46 46 Z" fill="#C83228" stroke="#2B1B12" stroke-width="2.8" stroke-linejoin="round"/>

                      <!-- 近側牛角（彎月形） -->
                      <path id="ox-horn-near" d="M 52 50 C 44 28, 52 10, 68 6 C 58 20, 56 36, 62 48 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>

                      <!-- 牛耳 -->
                      <path id="ox-ear" d="M 64 56 C 78 48, 92 52, 94 60 C 86 66, 74 66, 64 62 Z" fill="#E5A922" stroke="#2B1B12" stroke-width="2.3" stroke-linejoin="round"/>

                      <!-- 牛眼、眉骨與鼻孔 -->
                      <path d="M 28 66 Q 38 61 46 66" stroke="#2B1B12" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                      <ellipse cx="37" cy="72" rx="6" ry="4.5" fill="#FFFDF8" stroke="#2B1B12" stroke-width="2"/>
                      <circle cx="35" cy="72" r="2.8" fill="#2B1B12"/>
                      <circle cx="9" cy="91" r="2.5" fill="#2B1B12"/>

                      <!-- 牛口開／合 -->
                      <g id="ox-mouth-open">
                        <path d="M 4 101 Q 16 105 26 102 Q 22 114 10 113 Q 3 110 4 101 Z" fill="#7A1C16" stroke="#2B1B12" stroke-width="2"/>
                        <path d="M 9 107 Q 16 105 21 109" stroke="#E06D65" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                      </g>
                      <path id="ox-mouth-closed" d="M 4 102 Q 16 105 26 101" stroke="#2B1B12" stroke-width="2.6" fill="none" stroke-linecap="round" style="display:none;"/>

                      <!-- 籠頭、鼻環與苧繩結 -->
                      <path d="M 22 65 L 16 106 M 48 48 L 22 76" stroke="#5C3A21" stroke-width="2.4" fill="none"/>
                      <circle cx="5" cy="100" r="5.5" fill="none" stroke="#B8860B" stroke-width="2.4"/>
                      <path id="ox-rope" d="M 1 104 C -32 136, -82 134, -118 106" stroke="#2B6E4B" stroke-width="3.2" fill="none" stroke-dasharray="6,2"/>
                      <!-- 傳統苧繩結 -->
                      <circle id="ox-rope-knot" cx="-58" cy="126" r="5" fill="#2B6E4B" stroke="#2B1B12" stroke-width="1.5"/>
                    </g>

                    <!-- ================= 傳統版畫芒神圖層 (Mangshen) ================= -->
                    <g id="mangshen-group" transform="translate(95, 132)">
                      <!-- 褲管（著褲） -->
                      <path d="M 34 156 L 32 198 L 46 198 L 48 160 Z" fill="#3B4D59" stroke="#2B1B12" stroke-width="2.2"/>
                      <path d="M 52 160 L 54 198 L 68 198 L 66 156 Z" fill="#3B4D59" stroke="#2B1B12" stroke-width="2.2"/>

                      <!-- 赤腳（無行纏鞋子時顯示） -->
                      <g id="mangshen-barefoot">
                        <path d="M 33 198 L 24 212 L 45 214 L 45 198 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M 55 198 L 55 214 L 76 212 L 67 198 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2" stroke-linejoin="round"/>
                      </g>

                      <!-- 行纏與鞋子（有行纏鞋子時顯示） -->
                      <g id="mangshen-shoes" style="display:none;">
                        <!-- 行纏綁腿 -->
                        <rect x="32" y="182" width="14" height="18" fill="#E8DEC8" stroke="#2B1B12" stroke-width="1.8"/>
                        <line x1="32" y1="188" x2="46" y2="191" stroke="#2B1B12" stroke-width="1.4"/>
                        <line x1="32" y1="194" x2="46" y2="197" stroke="#2B1B12" stroke-width="1.4"/>
                        <rect x="54" y="182" width="14" height="18" fill="#E8DEC8" stroke="#2B1B12" stroke-width="1.8"/>
                        <line x1="54" y1="188" x2="68" y2="191" stroke="#2B1B12" stroke-width="1.4"/>
                        <line x1="54" y1="194" x2="68" y2="197" stroke="#2B1B12" stroke-width="1.4"/>
                        <!-- 傳統布鞋 -->
                        <path d="M 32 200 L 21 213 L 46 215 L 46 200 Z" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>
                        <path d="M 54 200 L 54 215 L 79 213 L 68 200 Z" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>
                      </g>

                      <!-- 芒神交領右衽寬袖長袍 -->
                      <path id="mangshen-clothes" d="M 34 58 L 66 58 L 84 88 L 74 104 L 68 92 L 74 166 L 26 166 L 32 92 L 26 104 L 16 88 Z" fill="#C83228" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
                      <!-- 交領衣襟線條 -->
                      <path d="M 38 58 L 54 84 M 62 58 L 44 88" stroke="#2B1B12" stroke-width="2" fill="none"/>
                      <!-- 衣褶水墨線 -->
                      <path d="M 40 120 L 36 162 M 52 122 L 52 163 M 62 120 L 65 162" stroke="#2B1B12" stroke-width="1.5" opacity="0.45"/>

                      <!-- 腰帶與飄帶 -->
                      <rect id="mangshen-belt" x="31" y="104" width="38" height="10" rx="2" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>
                      <path id="mangshen-belt-ribbon" d="M 45 114 L 41 152 L 47 148 L 50 114 M 53 114 L 58 150 L 52 147 L 50 114" fill="#2B2927" stroke="#2B1B12" stroke-width="1.6"/>

                      <!-- 雙手 -->
                      <circle cx="22" cy="98" r="5.5" fill="#F2C8A2" stroke="#2B1B12" stroke-width="1.8"/>
                      <circle cx="78" cy="98" r="5.5" fill="#F2C8A2" stroke="#2B1B12" stroke-width="1.8"/>

                      <!-- 頭部與雙髻 -->
                      <g id="mangshen-head">
                        <!-- 頭髮底廓 -->
                        <path d="M 32 32 C 32 16, 68 16, 68 32 Z" fill="#2B2927"/>
                        <!-- 平梳兩髻在頂真上 -->
                        <circle cx="38" cy="13" r="6.5" fill="#2B2927" stroke="#2B1B12" stroke-width="1.8"/>
                        <circle cx="62" cy="13" r="6.5" fill="#2B2927" stroke="#2B1B12" stroke-width="1.8"/>
                        <line x1="33" y1="17" x2="43" y2="17" stroke="#C83228" stroke-width="2"/>
                        <line x1="57" y1="17" x2="67" y2="17" stroke="#C83228" stroke-width="2"/>

                        <!-- 臉龐 -->
                        <path d="M 33 28 C 33 48, 42 57, 50 57 C 58 57, 67 48, 67 28 C 67 21, 33 21, 33 28 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2.2"/>

                        <!-- 罨耳（傳統護耳帽耳：支援全戴／揭起左邊／揭起右邊） -->
                        <!-- 畫面左側（芒神右耳） -->
                        <path id="ear-right-down" d="M 33 26 L 26 28 L 26 46 L 33 44 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>
                        <path id="ear-right-up" d="M 33 28 L 24 22 L 28 12 L 35 20 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8" style="display:none;"/>
                        <!-- 畫面右側（芒神左耳） -->
                        <path id="ear-left-down" d="M 67 26 L 74 28 L 74 46 L 67 44 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8" style="display:none;"/>
                        <path id="ear-left-up" d="M 67 28 L 76 22 L 72 12 L 65 20 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>

                        <!-- 五官（丹鳳眼、眉、鼻、口） -->
                        <path d="M 38 31 Q 42 28 46 31 M 54 31 Q 58 28 62 31" stroke="#2B1B12" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <circle cx="42" cy="35" r="2" fill="#2B1B12"/>
                        <circle cx="58" cy="35" r="2" fill="#2B1B12"/>
                        <path d="M 50 35 L 48 43 L 52 43" stroke="#2B1B12" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                        <path d="M 46 49 Q 50 51 54 49" stroke="#9E2A22" stroke-width="2" fill="none" stroke-linecap="round"/>

                        <!-- 老人長鬚（老人像時顯示） -->
                        <path id="mangshen-beard" d="M 42 52 C 40 68, 46 82, 50 86 C 54 82, 60 68, 58 52 Q 50 56 42 52 Z" fill="#F4EFE4" stroke="#7A736B" stroke-width="1.5" style="display:none;"/>
                      </g>

                      <!-- 柳枝鞭杖與五色絲結 -->
                      <g id="mangshen-whip">
                        <path d="M 78 96 Q 98 62 112 24" stroke="#6E4726" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <!-- 柳葉 -->
                        <path d="M 96 62 Q 108 54 114 60 Q 104 65 96 62 Z" fill="#4E6E38"/>
                        <path d="M 105 42 Q 94 34 89 40 Q 98 45 105 42 Z" fill="#4E6E38"/>
                        <!-- 五色絲結（青、紅、黃、白、黑） -->
                        <path d="M 112 24 C 122 32, 124 44, 118 52" stroke="#2B6E4B" stroke-width="2" fill="none"/>
                        <path d="M 112 24 C 126 28, 130 40, 125 48" stroke="#C83228" stroke-width="2" fill="none"/>
                        <path d="M 112 24 C 118 36, 116 48, 110 55" stroke="#E5A922" stroke-width="2" fill="none"/>
                        <path d="M 112 24 C 128 22, 134 32, 131 42" stroke="#F4EFE4" stroke-width="2" fill="none"/>
                        <path d="M 112 24 C 114 38, 110 48, 104 54" stroke="#2B2927" stroke-width="1.8" fill="none"/>
                      </g>
                    </g>

                    <!-- 通書朱砂雙層外框（壓在最上層確保邊界俐落） -->
                    <rect x="8" y="8" width="664" height="404" fill="none" stroke="#9E2A22" stroke-width="4"/>
                    <rect x="15" y="15" width="650" height="390" fill="none" stroke="#9E2A22" stroke-width="1.5"/>
                  </svg>
                </div>
              </div>

              <!-- 右側：春牛歌訣與五色對照圖例 -->
              <div class="col-lg-4">
                <div class="p-3 rounded springox-verse-box mb-3">
                  <?php echo '【' . htmlspecialchars($oxcontent ?? '') . '】'; ?>
                </div>

                <div class="mb-2 font-weight-bold" style="color: #5C3A21; font-size: 0.95rem;">服色五行圖例：</div>
                <div class="d-flex flex-wrap">
                  <span class="springox-legend-item">牛頭<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['head']; ?>"></i></span>
                  <span class="springox-legend-item">牛身<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['body']; ?>"></i></span>
                  <span class="springox-legend-item">牛腹<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['belly']; ?>"></i></span>
                  <span class="springox-legend-item">角耳尾<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['hornEarTail']; ?>"></i></span>
                  <span class="springox-legend-item">牛脛<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['leg']; ?>"></i></span>
                  <span class="springox-legend-item">牛蹄<i class="springox-color-dot" style="background:<?php echo $oxVisualData['ox']['hoof']; ?>"></i></span>
                  <span class="springox-legend-item">芒神衣<i class="springox-color-dot" style="background:<?php echo $oxVisualData['mangshen']['clothes']; ?>"></i></span>
                  <span class="springox-legend-item">腰帶<i class="springox-color-dot" style="background:<?php echo $oxVisualData['mangshen']['belt']; ?>"></i></span>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const data = <?php echo json_encode($oxVisualData, JSON_UNESCAPED_UNICODE); ?>;
    if (!data) return;

    // 1. 套用春牛顏色
    document.getElementById("ox-head").setAttribute("fill", data.ox.head);
    document.getElementById("ox-body").setAttribute("fill", data.ox.body);
    document.getElementById("ox-belly").setAttribute("fill", data.ox.belly);
    document.getElementById("ox-horn-near").setAttribute("fill", data.ox.hornEarTail);
    document.getElementById("ox-horn-far").setAttribute("fill", data.ox.hornEarTail);
    document.getElementById("ox-ear").setAttribute("fill", data.ox.hornEarTail);

    // 牛尾顏色與左繳／右繳方向
    document.getElementById("ox-tail-left-path").setAttribute("stroke", data.ox.hornEarTail);
    document.getElementById("ox-tail-left-tuft").setAttribute("fill", data.ox.hornEarTail);
    document.getElementById("ox-tail-right-path").setAttribute("stroke", data.ox.hornEarTail);
    document.getElementById("ox-tail-right-tuft").setAttribute("fill", data.ox.hornEarTail);

    document.getElementById("ox-tail-left").style.display = (data.ox.tailDir === "left") ? "block" : "none";
    document.getElementById("ox-tail-right").style.display = (data.ox.tailDir === "right") ? "block" : "none";

    // 四肢與牛蹄
    ["ox-leg-far-1", "ox-leg-far-2", "ox-leg-near-1", "ox-leg-near-2"].forEach(id => {
      document.getElementById(id).setAttribute("fill", data.ox.leg);
    });
    ["ox-hoof-far-1", "ox-hoof-far-2", "ox-hoof-near-1", "ox-hoof-near-2"].forEach(id => {
      document.getElementById(id).setAttribute("fill", data.ox.hoof);
    });

    // 苧繩色與牛口開合
    document.getElementById("ox-rope").setAttribute("stroke", data.ox.rope);
    document.getElementById("ox-rope-knot").setAttribute("fill", data.ox.rope);
    document.getElementById("ox-mouth-open").style.display = data.ox.mouthOpen ? "block" : "none";
    document.getElementById("ox-mouth-closed").style.display = data.ox.mouthOpen ? "none" : "block";

    // 2. 套用芒神服色、腰帶與鞋履
    document.getElementById("mangshen-clothes").setAttribute("fill", data.mangshen.clothes);
    document.getElementById("mangshen-belt").setAttribute("fill", data.mangshen.belt);
    document.getElementById("mangshen-belt-ribbon").setAttribute("fill", data.mangshen.belt);

    document.getElementById("mangshen-shoes").style.display = data.mangshen.hasShoes ? "block" : "none";
    document.getElementById("mangshen-barefoot").style.display = data.mangshen.hasShoes ? "none" : "block";

    // 3. 罨耳戴法（全戴／揭起左邊／揭起右邊／不戴）
    const ec = data.mangshen.earCover;
    document.getElementById("ear-right-down").style.display = (ec === "full" || ec === "lift_left") ? "block" : "none";
    document.getElementById("ear-right-up").style.display   = (ec === "lift_right") ? "block" : "none";
    document.getElementById("ear-left-down").style.display  = (ec === "full" || ec === "lift_right") ? "block" : "none";
    document.getElementById("ear-left-up").style.display    = (ec === "lift_left") ? "block" : "none";

    // 4. 年齡面貌與站位（牛前／並肩／牛後）
    document.getElementById("mangshen-beard").style.display = (data.mangshen.age === "old") ? "block" : "none";
    const scale = (data.mangshen.age === "child") ? 0.86 : 1.0;
    const posY  = (data.mangshen.age === "child") ? 162 : 132;
    document.getElementById("mangshen-group").setAttribute("transform", `translate(${data.mangshen.posX}, ${posY}) scale(${scale})`);
  });
  </script>
</body>
</html>
