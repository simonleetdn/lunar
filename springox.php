<?php
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

// 預設顯示今年，或使用者輸入的年份
$year = isset($_GET['year']) && !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// 初始化為農曆正月初一
$lunar = Lunar::fromYmd($year, 1, 1);
$solar = $lunar->getSolar();

include_once("springoxcon.php");

/**
 * 自動解析 $oxcontent 文字為 SVG 繪圖參數
 */
function parseSpringOx($text) {
    // 傳統五色色碼表
    $colorMap = [
        '青' => '#2E8B57', // 青色/綠色
        '綠' => '#2E8B57',
        '紅' => '#D9381E', // 紅色
        '赤' => '#D9381E',
        '黃' => '#F2C94C', // 黃色
        '白' => '#F5F5F5', // 白色
        '黑' => '#2C2C2C', // 黑色
        '皂' => '#2C2C2C'
    ];

    $getColor = function($pattern, $default = '#D9381E') use ($text, $colorMap) {
        if (preg_match($pattern, $text, $matches)) {
            $c = $matches[1];
            return $colorMap[$c] ?? $default;
        }
        return $default;
    };

    // 1. 解析春牛顏色與狀態
    $oxHead  = $getColor('/牛頭([青綠紅赤黃白黑皂])/u', '#D9381E');
    $oxBody  = $getColor('/牛身([青綠紅赤黃白黑皂])/u', '#D9381E');
    $oxBelly = $getColor('/牛腹([青綠紅赤黃白黑皂])/u', '#2C2C2C');
    $oxHorn  = $getColor('/牛角[、,，]?耳[、,，]?尾([青綠紅赤黃白黑皂])/u', '#F2C94C');
    $oxLeg   = $getColor('/牛脛([青綠紅赤黃白黑皂])/u', '#F5F5F5');
    $oxHoof  = $getColor('/牛蹄([青綠紅赤黃白黑皂])/u', '#F2C94C');
    $oxRope  = $getColor('/苧繩結([青綠紅赤黃白黑皂])/u', '#2E8B57');
    $mouthOpen = (mb_strpos($text, '牛口開') !== false);

    // 2. 解析芒神服色、鞋履與站位
    $msClothes = $getColor('/([青綠紅赤黃白黑皂])衣/u', '#D9381E');
    $msBelt    = $getColor('/繫([青綠紅赤黃白黑皂])腰帶/u', '#2C2C2C');
    
    // 判斷是否有穿鞋 (若出現「無行纏鞋子」或「赤腳」則為 false)
    $hasShoes = (mb_strpos($text, '無行纏鞋子') === false && mb_strpos($text, '無鞋') === false);

    // 判斷芒神站位 (牛前 / 牛中並肩 / 牛後)
    $posX = 95; // 預設牛前 (早忙)
    if (mb_strpos($text, '牛後') !== false || mb_strpos($text, '晚閒') !== false) {
        $posX = 480; // 站牛後
    } elseif (mb_strpos($text, '並肩') !== false || mb_strpos($text, '牛中') !== false || mb_strpos($text, '平立') !== false) {
        $posX = 280; // 站牛身旁
    }

    // 判斷芒神年齡 (少壯 / 老人 / 孩童)
    $age = 'young';
    if (mb_strpos($text, '老人') !== false) {
        $age = 'old';
    } elseif (mb_strpos($text, '孩童') !== false || mb_strpos($text, '小兒') !== false) {
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
            'mouthOpen' => $mouthOpen
        ],
        'mangshen' => [
            'clothes' => $msClothes,
            'belt' => $msBelt,
            'hasShoes' => $hasShoes,
            'posX' => $posX,
            'age' => $age
        ]
    ];
}

// 取得當前年份的春牛繪圖設定
$oxVisualData = parseSpringOx($oxcontent);
?>

<!-- Bootstrap 卡片區塊：左圖右文 -->
<div class="card shadow-sm mb-4" style="background-color: #FDFBF7; border: 1px solid #D4C4A8;">
  <div class="card-body">
    <div class="row align-items-center">
      
      <!-- 左側：動態春牛 SVG 圖 -->
      <div class="col-lg-7 text-center mb-3 mb-lg-0">
        <div class="p-2 rounded" style="background-color: #FAF6EE; border: 1px dashed #C8B598;">
          <svg id="spring-ox-svg" viewBox="0 0 600 310" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; max-height: 320px;">
            <!-- 地面陰影 -->
            <ellipse cx="300" cy="275" rx="250" ry="14" fill="#E5DEC9" />

            <!-- ================= 春牛圖層 ================= -->
            <g id="ox-group" transform="translate(215, 80)">
              <!-- 牛尾 -->
              <path id="ox-tail" d="M 220 60 Q 250 40 245 100" stroke="#F2C94C" stroke-width="8" fill="none" stroke-linecap="round"/>
              <!-- 牛脛 (四肢) -->
              <rect id="ox-leg-1" x="50" y="110" width="16" height="65" fill="#F5F5F5" stroke="#333" stroke-width="2"/>
              <rect id="ox-leg-2" x="85" y="110" width="16" height="65" fill="#F5F5F5" stroke="#333" stroke-width="2"/>
              <rect id="ox-leg-3" x="170" y="110" width="16" height="65" fill="#F5F5F5" stroke="#333" stroke-width="2"/>
              <rect id="ox-leg-4" x="200" y="110" width="16" height="65" fill="#F5F5F5" stroke="#333" stroke-width="2"/>
              <!-- 牛蹄 -->
              <rect id="ox-hoof-1" x="48" y="170" width="20" height="12" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <rect id="ox-hoof-2" x="83" y="170" width="20" height="12" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <rect id="ox-hoof-3" x="168" y="170" width="20" height="12" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <rect id="ox-hoof-4" x="198" y="170" width="20" height="12" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <!-- 牛身 -->
              <rect id="ox-body" x="40" y="40" width="185" height="85" rx="35" fill="#D9381E" stroke="#333" stroke-width="2"/>
              <!-- 牛腹 -->
              <path id="ox-belly" d="M 65 110 Q 130 135 195 110 Z" fill="#2C2C2C" />
              <!-- 牛頭 -->
              <circle id="ox-head" cx="40" cy="65" r="35" fill="#D9381E" stroke="#333" stroke-width="2"/>
              <!-- 牛角與耳 -->
              <path id="ox-horn" d="M 25 35 Q 10 10 35 25" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <ellipse id="ox-ear" cx="55" cy="42" rx="12" ry="6" fill="#F2C94C" stroke="#333" stroke-width="2"/>
              <!-- 牛眼與牛口 -->
              <circle cx="28" cy="58" r="4" fill="#000"/>
              <ellipse id="ox-mouth-open" cx="15" cy="80" rx="8" ry="5" fill="#800000" />
              <line id="ox-mouth-closed" x1="8" y1="80" x2="22" y2="80" stroke="#333" stroke-width="3" style="display:none;"/>
              <!-- 籠頭與拘繩 -->
              <path id="ox-rope" d="M 15 75 Q -20 110 -60 95" stroke="#2E8B57" stroke-width="3" fill="none" stroke-dasharray="4,2"/>
            </g>

            <!-- ================= 芒神圖層 ================= -->
            <g id="mangshen-group" transform="translate(95, 100)">
              <!-- 雙腿 -->
              <rect x="22" y="100" width="10" height="55" fill="#F5CBA7" stroke="#333" stroke-width="1.5"/>
              <rect x="38" y="100" width="10" height="55" fill="#F5CBA7" stroke="#333" stroke-width="1.5"/>
              <!-- 鞋子 -->
              <g id="mangshen-shoes" style="display:none;">
                <rect x="18" y="150" width="16" height="8" fill="#2C2C2C"/>
                <rect x="36" y="150" width="16" height="8" fill="#2C2C2C"/>
              </g>
              <!-- 芒神衣物 -->
              <path id="mangshen-clothes" d="M 15 45 L 55 45 L 62 110 L 8 110 Z" fill="#D9381E" stroke="#333" stroke-width="2"/>
              <!-- 腰帶 -->
              <rect id="mangshen-belt" x="13" y="80" width="44" height="8" fill="#2C2C2C"/>
              <!-- 頭部 -->
              <circle cx="35" cy="28" r="18" fill="#F5CBA7" stroke="#333" stroke-width="2"/>
              <!-- 老人鬍鬚 (若為老人像則顯示) -->
              <path id="mangshen-beard" d="M 28 42 Q 35 65 42 42 Z" fill="#FFFFFF" stroke="#999" stroke-width="1" style="display:none;"/>
              <!-- 髮髻 -->
              <circle cx="24" cy="10" r="6" fill="#222"/>
              <circle cx="46" cy="10" r="6" fill="#222"/>
              <!-- 罨耳 -->
              <path d="M 16 25 L 16 40 M 54 22 L 58 15" stroke="#555" stroke-width="4" stroke-linecap="round"/>
              <!-- 柳枝鞭杖與五色絲結 -->
              <line x1="55" y1="75" x2="95" y2="35" stroke="#8B5A2B" stroke-width="3"/>
              <circle cx="95" cy="35" r="4" fill="#D9381E"/>
              <circle cx="98" cy="38" r="3" fill="#F2C94C"/>
              <circle cx="92" cy="32" r="3" fill="#2E8B57"/>
            </g>
          </svg>
        </div>
        <small class="text-muted mt-1 d-block">※ 圖示依當年干支五行與立春時刻動態繪製</small>
      </div>

      <!-- 右側：春牛歌訣原文與色碼圖例 -->
      <div class="col-lg-5">
        <div class="p-3 rounded" style="background-color: #F4EFE6; border-left: 4px solid #DC3545; line-height: 1.8; font-size: 1.05rem;">
          <?php echo '【' . htmlspecialchars($oxcontent) . '】'; ?>
        </div>
        
        <!-- 顏色圖例快速對照 -->
        <div class="mt-3 d-flex flex-wrap gap-2" style="font-size: 0.9rem;">
          <span class="badge badge-light border mr-2 mb-1 p-2">
            牛頭：<span style="color: <?php echo $oxVisualData['ox']['head']; ?>;">■</span>
          </span>
          <span class="badge badge-light border mr-2 mb-1 p-2">
            牛身：<span style="color: <?php echo $oxVisualData['ox']['body']; ?>;">■</span>
          </span>
          <span class="badge badge-light border mr-2 mb-1 p-2">
            牛腹：<span style="color: <?php echo $oxVisualData['ox']['belly']; ?>;">■</span>
          </span>
          <span class="badge badge-light border mr-2 mb-1 p-2">
            芒神衣：<span style="color: <?php echo $oxVisualData['mangshen']['clothes']; ?>;">■</span>
          </span>
          <span class="badge badge-light border mr-2 mb-1 p-2">
            腰帶：<span style="color: <?php echo $oxVisualData['mangshen']['belt']; ?>;">■</span>
          </span>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  // 接收 PHP 解析好的 JSON 資料
  const data = <?php echo json_encode($oxVisualData, JSON_UNESCAPED_UNICODE); ?>;
  if (!data) return;

  // 1. 套用春牛顏色
  document.getElementById("ox-head").setAttribute("fill", data.ox.head);
  document.getElementById("ox-body").setAttribute("fill", data.ox.body);
  document.getElementById("ox-belly").setAttribute("fill", data.ox.belly);
  document.getElementById("ox-horn").setAttribute("fill", data.ox.hornEarTail);
  document.getElementById("ox-ear").setAttribute("fill", data.ox.hornEarTail);
  document.getElementById("ox-tail").setAttribute("stroke", data.ox.hornEarTail);
  document.getElementById("ox-rope").setAttribute("stroke", data.ox.rope);

  for (let i = 1; i <= 4; i++) {
    document.getElementById(`ox-leg-${i}`).setAttribute("fill", data.ox.leg);
    document.getElementById(`ox-hoof-${i}`).setAttribute("fill", data.ox.hoof);
  }

  // 牛口開合
  document.getElementById("ox-mouth-open").style.display = data.ox.mouthOpen ? "block" : "none";
  document.getElementById("ox-mouth-closed").style.display = data.ox.mouthOpen ? "none" : "block";

  // 2. 套用芒神服色、鞋子、鬍鬚與站位
  document.getElementById("mangshen-clothes").setAttribute("fill", data.mangshen.clothes);
  document.getElementById("mangshen-belt").setAttribute("fill", data.mangshen.belt);
  document.getElementById("mangshen-shoes").style.display = data.mangshen.hasShoes ? "block" : "none";
  document.getElementById("mangshen-beard").style.display = (data.mangshen.age === "old") ? "block" : "none";
  
  // 若為孩童像，稍微縮小芒神比例
  const scale = (data.mangshen.age === "child") ? 0.85 : 1.0;
  const posY = (data.mangshen.age === "child") ? 125 : 100;
  document.getElementById("mangshen-group").setAttribute("transform", `translate(${data.mangshen.posX}, ${posY}) scale(${scale})`);
});
</script>
