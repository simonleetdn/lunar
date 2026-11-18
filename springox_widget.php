<?php
// 確保有 $oxcontent 才渲染
if (!empty($oxcontent)):

if (!function_exists('parseSpringOx')) {
    function parseSpringOx($text) {
        $colorMap = [
            '青' => '#2B6E4B', '綠' => '#2B6E4B', '绿' => '#2B6E4B',
            '紅' => '#C83228', '红' => '#C83228', '赤' => '#C83228',
            '黃' => '#E5A922', '黄' => '#E5A922',
            '白' => '#F4EFE4',
            '黑' => '#2B2927', '皂' => '#2B2927'
        ];

        $getColor = function($pattern, $default = '#C83228') use ($text, $colorMap) {
            if (preg_match($pattern, $text, $matches)) {
                return $colorMap[$matches[1]] ?? $default;
            }
            return $default;
        };

        $oxHead  = $getColor('/牛頭([青綠绿紅红赤黃黄白黑皂])/u', '#C83228');
        $oxBody  = $getColor('/牛身([青綠绿紅红赤黃黄白黑皂])/u', '#C83228');
        $oxBelly = $getColor('/牛腹([青綠绿紅红赤黃黄白黑皂])/u', '#2B2927');
        $oxHorn  = $getColor('/牛角[、,，]?耳[、,，]?尾([青綠绿紅红赤黃黄白黑皂])/u', '#E5A922');
        $oxLeg   = $getColor('/牛脛([青綠绿紅红赤黃黄白黑皂])/u', '#F4EFE4');
        $oxHoof  = $getColor('/牛蹄([青綠绿紅红赤黃黄白黑皂])/u', '#E5A922');
        $oxRope  = $getColor('/苧繩結([青綠绿紅红赤黃黄白黑皂])/u', '#2B6E4B');

        $mouthOpen = (mb_strpos($text, '牛口開') !== false || mb_strpos($text, '牛口开') !== false);
        $tailDir   = (mb_strpos($text, '牛尾右繳') !== false || mb_strpos($text, '牛尾右缴') !== false) ? 'right' : 'left';

        $msClothes = $getColor('/([青綠绿紅红赤黃黄白黑皂])衣/u', '#C83228');
        $msBelt    = $getColor('/繫([青綠绿紅红赤黃黄白黑皂])腰帶/u', '#2B2927');

        $earCover = 'full';
        if (mb_strpos($text, '揭起左') !== false) {
            $earCover = 'lift_left';
        } elseif (mb_strpos($text, '揭起右') !== false) {
            $earCover = 'lift_right';
        } elseif (mb_strpos($text, '不戴') !== false || mb_strpos($text, '無罨耳') !== false) {
            $earCover = 'none';
        }

        $hasShoes = (
            mb_strpos($text, '無行纏鞋子') === false &&
            mb_strpos($text, '无行缠鞋子') === false &&
            mb_strpos($text, '無鞋') === false &&
            mb_strpos($text, '赤腳') === false
        );

        $posX = 95;
        if (mb_strpos($text, '牛後') !== false || mb_strpos($text, '牛后') !== false || mb_strpos($text, '晚閒') !== false || mb_strpos($text, '晚闲') !== false) {
            $posX = 505;
        } elseif (mb_strpos($text, '並肩') !== false || mb_strpos($text, '并肩') !== false || mb_strpos($text, '平立') !== false || mb_strpos($text, '牛中') !== false) {
            $posX = 300;
        }

        $age = 'young';
        if (mb_strpos($text, '老人') !== false) {
            $age = 'old';
        } elseif (mb_strpos($text, '孩童') !== false || mb_strpos($text, '小兒') !== false || mb_strpos($text, '小儿') !== false) {
            $age = 'child';
        }

        return [
            'ox' => [
                'head' => $oxHead, 'body' => $oxBody, 'belly' => $oxBelly,
                'hornEarTail' => $oxHorn, 'leg' => $oxLeg, 'hoof' => $oxHoof,
                'rope' => $oxRope, 'mouthOpen' => $mouthOpen, 'tailDir' => $tailDir
            ],
            'mangshen' => [
                'clothes' => $msClothes, 'belt' => $msBelt, 'earCover' => $earCover,
                'hasShoes' => $hasShoes, 'posX' => $posX, 'age' => $age
            ]
        ];
    }
}

$oxData = parseSpringOx($oxcontent);
$gzTitle = '';
if (!empty($ly)) {
    $gzTitle = $ly;
} elseif (!empty($ganZhiYear)) {
    $gzTitle = $ganZhiYear;
} elseif (isset($lunar) && method_exists($lunar, 'getYearInGanZhi')) {
    $gzTitle = $lunar->getYearInGanZhi();
}
$gzChars = preg_split('//u', $gzTitle, -1, PREG_SPLIT_NO_EMPTY);
$gz1 = $gzChars[0] ?? '';
$gz2 = $gzChars[1] ?? '';
$uid = 'ox_' . uniqid(); // 避免同頁面多次載入時 ID 衝突
?>

<style>
  .springox-widget-box {
    max-width: 560px;
    margin: 12px 0;
    background: #FAF3E3;
    border: 2px solid #9E2A22;
    border-radius: 6px;
    padding: 8px;
    box-shadow: 0 3px 10px rgba(60, 35, 15, 0.12);
    clear: both;
  }
  .springox-widget-box svg {
    display: block;
    width: 100%;
    height: auto;
  }
</style>

<div class="springox-widget-box" id="<?php echo $uid; ?>">
  <svg viewBox="0 0 680 420" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <radialGradient id="xuan-paper-<?php echo $uid; ?>" cx="50%" cy="45%" r="65%">
        <stop offset="0%" stop-color="#FBF5E6"/>
        <stop offset="70%" stop-color="#F3E5C8"/>
        <stop offset="100%" stop-color="#E5D1AA"/>
      </radialGradient>
      <linearGradient id="hill-ink-<?php echo $uid; ?>" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%" stop-color="#D5C3A1" stop-opacity="0.55"/>
        <stop offset="100%" stop-color="#EFE0C2" stop-opacity="0"/>
      </linearGradient>
    </defs>

    <!-- 背景紙張與遠山祥雲 -->
    <rect x="0" y="0" width="680" height="420" fill="url(#xuan-paper-<?php echo $uid; ?>)"/>
    <path d="M 16 260 Q 110 195 220 245 Q 330 185 450 240 Q 560 190 664 250 L 664 345 L 16 345 Z" fill="url(#hill-ink-<?php echo $uid; ?>)"/>
    <g fill="none" stroke="#B89F78" stroke-width="1.8" opacity="0.75">
      <path d="M 48 65 C 62 52, 85 52, 95 65 C 108 58, 128 62, 134 74 C 115 78, 82 78, 52 75 Z"/>
      <path d="M 72 82 C 92 78, 125 78, 152 84"/>
      <path d="M 330 48 C 345 36, 368 38, 378 50 C 392 44, 412 48, 418 58 C 395 62, 360 62, 332 58 Z"/>
    </g>

    <!-- 大地與草坡 -->
    <rect x="16" y="344" width="648" height="60" fill="#E6D2AE"/>
    <path d="M 16 344 Q 180 338 340 345 Q 500 352 664 343" stroke="#6E553A" stroke-width="2" fill="none"/>
    <g stroke="#4E5B31" stroke-width="1.8" fill="none" stroke-linecap="round">
      <path d="M 65 352 L 58 338 M 68 352 L 69 335 M 72 352 L 80 340"/>
      <path d="M 275 358 L 268 345 M 279 358 L 281 342 M 283 358 L 291 346"/>
      <path d="M 555 354 L 548 340 M 559 354 L 560 337 M 563 354 L 572 342"/>
    </g>

    <!-- 右上角直書牌匾與朱文印章 -->
    <g transform="translate(598, 30)">
      <rect x="0" y="0" width="48" height="156" fill="#FAF2DF" stroke="#9E2A22" stroke-width="2"/>
      <rect x="3" y="3" width="42" height="150" fill="none" stroke="#9E2A22" stroke-width="0.8"/>
      <text x="24" y="26" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12"><?php echo htmlspecialchars($gz1); ?></text>
      <text x="24" y="50" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12"><?php echo htmlspecialchars($gz2); ?></text>
      <text x="24" y="74" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#2B1B12">年</text>
      <text x="24" y="98" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">春</text>
      <text x="24" y="122" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">牛</text>
      <text x="24" y="146" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="18" font-weight="bold" fill="#9E2A22">圖</text>
      <rect x="10" y="166" width="28" height="28" rx="2" fill="#B82921"/>
      <text x="24" y="179" text-anchor="middle" font-family="'Noto Serif TC', serif" font-size="10" font-weight="bold" fill="#FAF2DF">迎春</text>
      <text x="24" y="190" text-anchor="middle" font-family="'Noto Serif TC', serif" font-size="10" font-weight="bold" fill="#FAF2DF">大吉</text>
    </g>

    <!-- 春牛圖層 -->
    <g transform="translate(230, 128)">
      <!-- 牛尾（左繳／右繳） -->
      <g style="display:<?php echo $oxData['ox']['tailDir'] === 'left' ? 'block' : 'none'; ?>;">
        <path d="M 244 76 C 266 94, 256 144, 228 160" stroke="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke-width="7" fill="none" stroke-linecap="round"/>
        <path d="M 244 76 C 266 94, 256 144, 228 160" stroke="#2B1B12" stroke-width="2" fill="none"/>
        <path d="M 228 160 C 218 166, 214 176, 222 182 C 230 178, 236 170, 234 160 Z" fill="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke="#2B1B12" stroke-width="2"/>
      </g>
      <g style="display:<?php echo $oxData['ox']['tailDir'] === 'right' ? 'block' : 'none'; ?>;">
        <path d="M 244 76 C 278 86, 286 135, 268 165" stroke="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke-width="7" fill="none" stroke-linecap="round"/>
        <path d="M 244 76 C 278 86, 286 135, 268 165" stroke="#2B1B12" stroke-width="2" fill="none"/>
        <path d="M 268 165 C 262 174, 266 184, 275 182 C 280 175, 278 166, 272 162 Z" fill="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke="#2B1B12" stroke-width="2"/>
      </g>

      <!-- 遠側牛脛與牛蹄 -->
      <path d="M 68 130 L 62 172 L 66 206 L 82 206 L 80 172 L 86 132 Z" fill="<?php echo $oxData['ox']['leg']; ?>" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>
      <path d="M 192 132 L 198 170 L 190 206 L 206 206 L 214 170 L 210 132 Z" fill="<?php echo $oxData['ox']['leg']; ?>" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>
      <path d="M 65 206 L 61 218 L 83 218 L 82 206 Z" fill="<?php echo $oxData['ox']['hoof']; ?>" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>
      <path d="M 189 206 L 185 218 L 207 218 L 206 206 Z" fill="<?php echo $oxData['ox']['hoof']; ?>" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>

      <!-- 牛身與牛腹 -->
      <path d="M 54 68 C 72 38, 112 34, 146 52 C 178 62, 222 54, 246 76 C 258 94, 254 128, 234 144 C 208 158, 165 160, 120 158 C 84 156, 56 144, 48 118 Z" fill="<?php echo $oxData['ox']['body']; ?>" stroke="#2B1B12" stroke-width="2.8" stroke-linejoin="round"/>
      <path d="M 78 122 C 118 134, 174 134, 214 120 C 204 148, 176 159, 145 159 C 112 159, 86 147, 78 122 Z" fill="<?php echo $oxData['ox']['belly']; ?>" stroke="#2B1B12" stroke-width="2"/>

      <!-- 肌理墨線 -->
      <g stroke="#2B1B12" stroke-width="1.6" fill="none" opacity="0.45" stroke-linecap="round">
        <path d="M 105 64 C 98 82, 98 102, 106 116"/>
        <path d="M 136 74 C 132 88, 132 102, 136 112"/>
        <path d="M 150 76 C 146 90, 146 104, 150 114"/>
        <path d="M 164 76 C 160 90, 160 104, 164 114"/>
        <path d="M 216 74 C 206 90, 206 110, 216 124"/>
      </g>

      <!-- 近側牛脛與牛蹄 -->
      <path d="M 88 128 L 82 174 L 86 212 L 103 212 L 101 174 L 108 130 Z" fill="<?php echo $oxData['ox']['leg']; ?>" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
      <path d="M 212 126 L 222 170 L 214 212 L 231 212 L 239 170 L 232 126 Z" fill="<?php echo $oxData['ox']['leg']; ?>" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
      <path d="M 85 212 L 80 225 L 93 225 L 95 219 L 97 225 L 105 225 L 103 212 Z" fill="<?php echo $oxData['ox']['hoof']; ?>" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>
      <path d="M 213 212 L 208 225 L 221 225 L 223 219 L 225 225 L 233 225 L 231 212 Z" fill="<?php echo $oxData['ox']['hoof']; ?>" stroke="#2B1B12" stroke-width="2.2" stroke-linejoin="round"/>

      <!-- 牛頭、角、耳 -->
      <path d="M 38 52 C 26 32, 28 14, 42 8 C 36 22, 38 36, 48 48 Z" fill="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke="#2B1B12" stroke-width="2.3" stroke-linejoin="round"/>
      <path d="M 46 46 C 62 46, 74 58, 72 78 C 70 98, 56 112, 36 114 C 18 116, 4 108, 2 96 C 0 84, 12 72, 24 62 C 32 54, 38 46, 46 46 Z" fill="<?php echo $oxData['ox']['head']; ?>" stroke="#2B1B12" stroke-width="2.8" stroke-linejoin="round"/>
      <path d="M 52 50 C 44 28, 52 10, 68 6 C 58 20, 56 36, 62 48 Z" fill="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke="#2B1B12" stroke-width="2.4" stroke-linejoin="round"/>
      <path d="M 64 56 C 78 48, 92 52, 94 60 C 86 66, 74 66, 64 62 Z" fill="<?php echo $oxData['ox']['hornEarTail']; ?>" stroke="#2B1B12" stroke-width="2.3" stroke-linejoin="round"/>

      <!-- 眼鼻口與籠頭繩 -->
      <path d="M 28 66 Q 38 61 46 66" stroke="#2B1B12" stroke-width="2.2" fill="none" stroke-linecap="round"/>
      <ellipse cx="37" cy="72" rx="6" ry="4.5" fill="#FFFDF8" stroke="#2B1B12" stroke-width="2"/>
      <circle cx="35" cy="72" r="2.8" fill="#2B1B12"/>
      <circle cx="9" cy="91" r="2.5" fill="#2B1B12"/>

      <?php if ($oxData['ox']['mouthOpen']): ?>
        <path d="M 4 101 Q 16 105 26 102 Q 22 114 10 113 Q 3 110 4 101 Z" fill="#7A1C16" stroke="#2B1B12" stroke-width="2"/>
        <path d="M 9 107 Q 16 105 21 109" stroke="#E06D65" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <?php else: ?>
        <path d="M 4 102 Q 16 105 26 101" stroke="#2B1B12" stroke-width="2.6" fill="none" stroke-linecap="round"/>
      <?php endif; ?>

      <path d="M 22 65 L 16 106 M 48 48 L 22 76" stroke="#5C3A21" stroke-width="2.4" fill="none"/>
      <circle cx="5" cy="100" r="5.5" fill="none" stroke="#B8860B" stroke-width="2.4"/>
      <path d="M 1 104 C -32 136, -82 134, -118 106" stroke="<?php echo $oxData['ox']['rope']; ?>" stroke-width="3.2" fill="none" stroke-dasharray="6,2"/>
      <circle cx="-58" cy="126" r="5" fill="<?php echo $oxData['ox']['rope']; ?>" stroke="#2B1B12" stroke-width="1.5"/>
    </g>

    <!-- 芒神圖層 -->
    <?php
      $msScale = ($oxData['mangshen']['age'] === 'child') ? 0.86 : 1.0;
      $msY = ($oxData['mangshen']['age'] === 'child') ? 162 : 132;
      $ec = $oxData['mangshen']['earCover'];
    ?>
    <g transform="translate(<?php echo $oxData['mangshen']['posX']; ?>, <?php echo $msY; ?>) scale(<?php echo $msScale; ?>)">
      <!-- 褲管 -->
      <path d="M 34 156 L 32 198 L 46 198 L 48 160 Z" fill="#3B4D59" stroke="#2B1B12" stroke-width="2.2"/>
      <path d="M 52 160 L 54 198 L 68 198 L 66 156 Z" fill="#3B4D59" stroke="#2B1B12" stroke-width="2.2"/>

      <!-- 鞋履或赤腳 -->
      <?php if (!$oxData['mangshen']['hasShoes']): ?>
        <path d="M 33 198 L 24 212 L 45 214 L 45 198 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2" stroke-linejoin="round"/>
        <path d="M 55 198 L 55 214 L 76 212 L 67 198 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2" stroke-linejoin="round"/>
      <?php else: ?>
        <rect x="32" y="182" width="14" height="18" fill="#E8DEC8" stroke="#2B1B12" stroke-width="1.8"/>
        <line x1="32" y1="188" x2="46" y2="191" stroke="#2B1B12" stroke-width="1.4"/>
        <line x1="32" y1="194" x2="46" y2="197" stroke="#2B1B12" stroke-width="1.4"/>
        <rect x="54" y="182" width="14" height="18" fill="#E8DEC8" stroke="#2B1B12" stroke-width="1.8"/>
        <line x1="54" y1="188" x2="68" y2="191" stroke="#2B1B12" stroke-width="1.4"/>
        <line x1="54" y1="194" x2="68" y2="197" stroke="#2B1B12" stroke-width="1.4"/>
        <path d="M 32 200 L 21 213 L 46 215 L 46 200 Z" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>
        <path d="M 54 200 L 54 215 L 79 213 L 68 200 Z" fill="#2B2927" stroke="#2B1B12" stroke-width="2"/>
      <?php endif; ?>

      <!-- 衣袍與腰帶 -->
      <path d="M 34 58 L 66 58 L 84 88 L 74 104 L 68 92 L 74 166 L 26 166 L 32 92 L 26 104 L 16 88 Z" fill="<?php echo $oxData['mangshen']['clothes']; ?>" stroke="#2B1B12" stroke-width="2.5" stroke-linejoin="round"/>
      <path d="M 38 58 L 54 84 M 62 58 L 44 88" stroke="#2B1B12" stroke-width="2" fill="none"/>
      <path d="M 40 120 L 36 162 M 52 122 L 52 163 M 62 120 L 65 162" stroke="#2B1B12" stroke-width="1.5" opacity="0.45"/>

      <rect x="31" y="104" width="38" height="10" rx="2" fill="<?php echo $oxData['mangshen']['belt']; ?>" stroke="#2B1B12" stroke-width="2"/>
      <path d="M 45 114 L 41 152 L 47 148 L 50 114 M 53 114 L 58 150 L 52 147 L 50 114" fill="<?php echo $oxData['mangshen']['belt']; ?>" stroke="#2B1B12" stroke-width="1.6"/>

      <circle cx="22" cy="98" r="5.5" fill="#F2C8A2" stroke="#2B1B12" stroke-width="1.8"/>
      <circle cx="78" cy="98" r="5.5" fill="#F2C8A2" stroke="#2B1B12" stroke-width="1.8"/>

      <!-- 頭部、雙髻與罨耳 -->
      <path d="M 32 32 C 32 16, 68 16, 68 32 Z" fill="#2B2927"/>
      <circle cx="38" cy="13" r="6.5" fill="#2B2927" stroke="#2B1B12" stroke-width="1.8"/>
      <circle cx="62" cy="13" r="6.5" fill="#2B2927" stroke="#2B1B12" stroke-width="1.8"/>
      <line x1="33" y1="17" x2="43" y2="17" stroke="#C83228" stroke-width="2"/>
      <line x1="57" y1="17" x2="67" y2="17" stroke="#C83228" stroke-width="2"/>

      <path d="M 33 28 C 33 48, 42 57, 50 57 C 58 57, 67 48, 67 28 C 67 21, 33 21, 33 28 Z" fill="#F2C8A2" stroke="#2B1B12" stroke-width="2.2"/>

      <?php if ($ec === 'full' || $ec === 'lift_left'): ?>
        <path d="M 33 26 L 26 28 L 26 46 L 33 44 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>
      <?php elseif ($ec === 'lift_right'): ?>
        <path d="M 33 28 L 24 22 L 28 12 L 35 20 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>
      <?php endif; ?>

      <?php if ($ec === 'full' || $ec === 'lift_right'): ?>
        <path d="M 67 26 L 74 28 L 74 46 L 67 44 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>
      <?php elseif ($ec === 'lift_left'): ?>
        <path d="M 67 28 L 76 22 L 72 12 L 65 20 Z" fill="#4A3B32" stroke="#2B1B12" stroke-width="1.8"/>
      <?php endif; ?>

      <!-- 五官與鬍鬚 -->
      <path d="M 38 31 Q 42 28 46 31 M 54 31 Q 58 28 62 31" stroke="#2B1B12" stroke-width="1.8" fill="none" stroke-linecap="round"/>
      <circle cx="42" cy="35" r="2" fill="#2B1B12"/>
      <circle cx="58" cy="35" r="2" fill="#2B1B12"/>
      <path d="M 50 35 L 48 43 L 52 43" stroke="#2B1B12" stroke-width="1.6" fill="none" stroke-linecap="round"/>
      <path d="M 46 49 Q 50 51 54 49" stroke="#9E2A22" stroke-width="2" fill="none" stroke-linecap="round"/>

      <?php if ($oxData['mangshen']['age'] === 'old'): ?>
        <path d="M 42 52 C 40 68, 46 82, 50 86 C 54 82, 60 68, 58 52 Q 50 56 42 52 Z" fill="#F4EFE4" stroke="#7A736B" stroke-width="1.5"/>
      <?php endif; ?>

      <!-- 柳枝鞭杖與五色絲結 -->
      <path d="M 78 96 Q 98 62 112 24" stroke="#6E4726" stroke-width="3" fill="none" stroke-linecap="round"/>
      <path d="M 96 62 Q 108 54 114 60 Q 104 65 96 62 Z" fill="#4E6E38"/>
      <path d="M 105 42 Q 94 34 89 40 Q 98 45 105 42 Z" fill="#4E6E38"/>
      <path d="M 112 24 C 122 32, 124 44, 118 52" stroke="#2B6E4B" stroke-width="2" fill="none"/>
      <path d="M 112 24 C 126 28, 130 40, 125 48" stroke="#C83228" stroke-width="2" fill="none"/>
      <path d="M 112 24 C 118 36, 116 48, 110 55" stroke="#E5A922" stroke-width="2" fill="none"/>
      <path d="M 112 24 C 128 22, 134 32, 131 42" stroke="#F4EFE4" stroke-width="2" fill="none"/>
      <path d="M 112 24 C 114 38, 110 48, 104 54" stroke="#2B2927" stroke-width="1.8" fill="none"/>
    </g>

    <!-- 朱砂雙層外框 -->
    <rect x="8" y="8" width="664" height="404" fill="none" stroke="#9E2A22" stroke-width="4"/>
    <rect x="15" y="15" width="650" height="390" fill="none" stroke="#9E2A22" stroke-width="1.5"/>
  </svg>
</div>
<?php endif; ?>
