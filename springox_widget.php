<?php
/**
 * 傳統中國水墨春牛圖與芒神動態生成器
 * 風格：融合明清牧牛圖、古典白描人物畫與傳統通書年畫神韻
 * 畫布尺寸：680 x 420
 */

if (!function_exists('parseSpringOx')) {
    /**
     * 解析《春牛經》/ 通書春牛與芒神文字描述
     *
     * @param string $text 春牛與芒神描述文本
     * @return array 解析後的結構化設色、姿態與站位資料
     */
    function parseSpringOx($text) {
        // 傳統中國畫礦物設色與松煙墨色票（隨類賦彩）
        $colorMap = [
            '青' => '#2C634B', '綠' => '#2C634B', '绿' => '#2C634B', // 石青 / 花青
            '紅' => '#B5382A', '红' => '#B5382A', '赤' => '#B5382A', // 朱砂 / 銀朱
            '黃' => '#D19326', '黄' => '#D19326',                    // 藤黃 / 雄黃
            '白' => '#EFE8D8',                                       // 胡粉 / 宣紙白
            '黑' => '#2B2927', '皂' => '#2B2927',                    // 松煙濃墨
        ];

        $cPat = '[青綠绿紅红赤黃黄白黑皂]';

        $getColor = function ($pattern, $default = '#B5382A') use ($text, $colorMap) {
            if (preg_match($pattern, $text, $m)) {
                return $colorMap[$m[1]] ?? $default;
            }
            return $default;
        };

        // 1. 春牛各部位設色（支援「牛角、耳、尾X色」合併寫法，亦支援個別指定）
        $oxHead  = $getColor('/牛頭(' . $cPat . ')/u', '#B5382A');
        $oxBody  = $getColor('/牛身(' . $cPat . ')/u', '#B5382A');
        $oxBelly = $getColor('/牛腹(' . $cPat . ')/u', '#2B2927');

        $groupedHet = $getColor('/牛角[、,，]?耳[、,，]?尾(' . $cPat . ')/u', '#D19326');
        $oxHorn = $getColor('/牛角(' . $cPat . ')/u', $groupedHet);
        $oxEar  = $getColor('/牛耳(' . $cPat . ')/u', $groupedHet);
        $oxTail = $getColor('/牛尾(' . $cPat . ')/u', $groupedHet);

        $oxLeg  = $getColor('/牛脛(' . $cPat . ')/u', '#EFE8D8');
        $oxHoof = $getColor('/牛蹄(' . $cPat . ')/u', '#D19326');
        $oxRope = $getColor('/苧繩結(' . $cPat . ')/u', '#2C634B');

        // 2. 春牛口開合與牛尾左繳/右繳
        $mouthOpen = (strpos($text, '牛口開') !== false || strpos($text, '牛口开') !== false);
        if (strpos($text, '牛口合') !== false || strpos($text, '牛口閉') !== false || strpos($text, '牛口闭') !== false) {
            $mouthOpen = false;
        }
        $tailDir = (strpos($text, '牛尾右繳') !== false || strpos($text, '牛尾右缴') !== false) ? 'right' : 'left';

        // 3. 芒神服飾、腰帶、罨耳與鞋履
        $msClothes = $getColor('/(' . $cPat . ')衣/u', '#B5382A');
        $msBelt    = $getColor('/繫(' . $cPat . ')腰帶/u', '#2B2927');

        $earCover = 'full';
        if (strpos($text, '揭起左') !== false) {
            $earCover = 'lift_left';
        } elseif (strpos($text, '揭起右') !== false) {
            $earCover = 'lift_right';
        } elseif (strpos($text, '不戴') !== false || strpos($text, '無罨耳') !== false || strpos($text, '无罨耳') !== false) {
            $earCover = 'none';
        }

        $hasShoes = true;
        foreach (['無行纏鞋子', '无行缠鞋子', '無鞋', '赤腳'] as $bareKw) {
            if (strpos($text, $bareKw) !== false) {
                $hasShoes = false;
                break;
            }
        }

        // 4. 芒神六大站位座標（依立春早晚與年支陰陽決定）
        $side = (strpos($text, '牛右') !== false) ? 'right' : 'left';
        $zone = 'front';
        foreach (['牛後', '牛后', '牛左後', '牛右後', '晚閒', '晚闲'] as $backKw) {
            if (strpos($text, $backKw) !== false) {
                $zone = 'back';
                break;
            }
        }
        if ($zone === 'front') {
            foreach (['並肩', '并肩', '平立', '牛中', '牛左中', '牛右中'] as $midKw) {
                if (strpos($text, $midKw) !== false) {
                    $zone = 'middle';
                    break;
                }
            }
        }

        $posMap = [
            'front_left'   => [132, 350], // 牛左前邊（前景領牛）
            'front_right'  => [194, 332], // 牛右前邊（遠景牛頭前側）
            'middle_left'  => [362, 355], // 牛左中（前景並肩）
            'middle_right' => [392, 300], // 牛右中（遠景並肩，頭肩與雙髻完整露於牛背鞍部之上）
            'back_left'    => [542, 350], // 牛左後邊（前景策牛）
            'back_right'   => [528, 332], // 牛右後邊（遠景牛臀後側）
        ];
        $posKey = $zone . '_' . $side;
        [$posX, $posY] = $posMap[$posKey] ?? [132, 350];

        // 5. 芒神年齡相貌（少壯 / 老人 / 孩童）
        $age = 'young';
        if (strpos($text, '老人') !== false) {
            $age = 'old';
        } elseif (strpos($text, '孩童') !== false || strpos($text, '小兒') !== false || strpos($text, '小儿') !== false) {
            $age = 'child';
        }

        return [
            'ox' => [
                'head'      => $oxHead,
                'body'      => $oxBody,
                'belly'     => $oxBelly,
                'horn'      => $oxHorn,
                'ear'       => $oxEar,
                'tail'      => $oxTail,
                'leg'       => $oxLeg,
                'hoof'      => $oxHoof,
                'rope'      => $oxRope,
                'mouthOpen' => $mouthOpen,
                'tailDir'   => $tailDir,
            ],
            'mangshen' => [
                'clothes'  => $msClothes,
                'belt'     => $msBelt,
                'earCover' => $earCover,
                'hasShoes' => $hasShoes,
                'posX'     => $posX,
                'posY'     => $posY,
                'side'     => $side,
                'zone'     => $zone,
                'posKey'   => $posKey,
                'age'      => $age,
            ],
        ];
    }
}

if (!function_exists('springOxAdjustHex')) {
    /**
     * 計算礦物顏料深淺明暗色階（用於積墨與暈染層次）
     */
    function springOxAdjustHex($hexColor, $factor) {
        $hex = ltrim($hexColor, '#');
        if (strlen($hex) !== 6) {
            return $hexColor;
        }
        $r = max(0, min(255, (int) round(hexdec(substr($hex, 0, 2)) * $factor)));
        $g = max(0, min(255, (int) round(hexdec(substr($hex, 2, 2)) * $factor)));
        $b = max(0, min(255, (int) round(hexdec(substr($hex, 4, 2)) * $factor)));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}

if (!function_exists('renderMangshenFigureSvg')) {
    /**
     * 繪製古典工筆白描芒神（含雙髻、罨耳動態、年齡長鬚、蘭葉描衣紋、行纏鞋子與五色絲結柳枝）
     */
    function renderMangshenFigureSvg($oxData, $uid) {
        $ms = $oxData['mangshen'];
        $isRight = ($ms['side'] === 'right');

        if ($ms['age'] === 'child') {
            $baseScale = ($ms['posKey'] === 'middle_right') ? 0.89 : 0.84;
        } else {
            $baseScale = 1.0;
        }
        $scale = $baseScale * ($isRight ? 0.94 : 1.0);
        $scaleStr = number_format($scale, 3, '.', '');

        $ec     = $ms['earCover'];
        $cMain  = $ms['clothes'];
        $cDark  = springOxAdjustHex($cMain, 0.72);
        $bMain  = $ms['belt'];

        // 鞋履 / 赤腳（地面接觸點為 y = 0）
        if (!$ms['hasShoes']) {
            $footSvg = <<<SVG
            <!-- 赤腳與腳踝（白描細線） -->
            <path d="M -13 -16 L -14 -7 C -18 -4, -24 -2, -25 0 C -21 2, -10 2, -6 0 L -6 -16 Z" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.3" stroke-linejoin="round"/>
            <path d="M -22 -1 L -20 1 M -19 -1 L -17 1 M -16 -1 L -14 1" stroke="#1E1B18" stroke-width="0.8"/>
            <path d="M 5 -16 L 5 0 C 9 2, 20 2, 24 0 C 23 -2, 17 -4, 14 -7 L 13 -16 Z" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.3" stroke-linejoin="round"/>
            <path d="M 21 -1 L 19 1 M 18 -1 L 16 1 M 15 -1 L 13 1" stroke="#1E1B18" stroke-width="0.8"/>
SVG;
        } else {
            $footSvg = <<<SVG
            <!-- 傳統行纏綁腿與雲頭布鞋 -->
            <path d="M -15 -34 L -15 -9 L -5 -9 L -5 -34 Z" fill="#E5DEC9" stroke="#1E1B18" stroke-width="1.2"/>
            <path d="M -15 -29 L -5 -25 M -5 -29 L -15 -25 M -15 -20 L -5 -16 M -5 -20 L -15 -16" stroke="#5A4E3F" stroke-width="1.0"/>
            <path d="M 5 -34 L 5 -9 L 15 -9 L 15 -34 Z" fill="#E5DEC9" stroke="#1E1B18" stroke-width="1.2"/>
            <path d="M 5 -29 L 15 -25 M 15 -29 L 5 -25 M 5 -20 L 15 -16 M 15 -20 L 5 -16" stroke="#5A4E3F" stroke-width="1.0"/>
            <path d="M -15 -10 L -24 -3 C -26 -1, -25 2, -20 2 L -5 2 L -5 -10 Z" fill="#2E2B28" stroke="#1E1B18" stroke-width="1.3" stroke-linejoin="round"/>
            <path d="M -22 -2 Q -14 -4 -6 -2" stroke="#D8CEBE" stroke-width="1.0" fill="none"/>
            <path d="M 5 -10 L 5 2 L 20 2 C 25 2, 26 -1, 24 -3 L 15 -10 Z" fill="#2E2B28" stroke="#1E1B18" stroke-width="1.3" stroke-linejoin="round"/>
            <path d="M 6 -2 Q 14 -4 22 -2" stroke="#D8CEBE" stroke-width="1.0" fill="none"/>
SVG;
        }

        // 罨耳動態（全戴 / 揭起左邊 / 揭起右邊 / 不戴）
        $earSvg = '';
        if ($ec === 'full' || $ec === 'lift_left') {
            $earSvg .= <<<SVG
            <path d="M -12 -170 C -19 -168, -21 -156, -18 -148 C -15 -146, -11 -149, -11 -154 Z" fill="#3B342E" stroke="#1E1B18" stroke-width="1.2"/>
            <path d="M -16 -166 L -15 -151" stroke="#8C7B68" stroke-width="0.9"/>
SVG;
        } elseif ($ec === 'lift_right') {
            $earSvg .= <<<SVG
            <path d="M -11 -163 C -15 -164, -16 -155, -12 -153" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.1"/>
            <path d="M -12 -170 C -20 -174, -21 -184, -14 -186 C -10 -182, -10 -175, -11 -170 Z" fill="#3B342E" stroke="#1E1B18" stroke-width="1.2"/>
SVG;
        } else {
            $earSvg .= '<path d="M -11 -163 C -15 -164, -16 -155, -12 -153" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.1"/>';
        }

        if ($ec === 'full' || $ec === 'lift_right') {
            $earSvg .= <<<SVG
            <path d="M 12 -170 C 19 -168, 21 -156, 18 -148 C 15 -146, 11 -149, 11 -154 Z" fill="#3B342E" stroke="#1E1B18" stroke-width="1.2"/>
            <path d="M 16 -166 L 15 -151" stroke="#8C7B68" stroke-width="0.9"/>
SVG;
        } elseif ($ec === 'lift_left') {
            $earSvg .= <<<SVG
            <path d="M 11 -163 C 15 -164, 16 -155, 12 -153" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.1"/>
            <path d="M 12 -170 C 20 -174, 21 -184, 14 -186 C 10 -182, 10 -175, 11 -170 Z" fill="#3B342E" stroke="#1E1B18" stroke-width="1.2"/>
SVG;
        } else {
            $earSvg .= '<path d="M 11 -163 C 15 -164, 16 -155, 12 -153" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.1"/>';
        }

        // 年齡特徵（老人長鬚與眉紋）
        $ageSvg = '';
        if ($ms['age'] === 'old') {
            $ageSvg = <<<SVG
            <!-- 老人長鬚與眉紋（工筆白描絲毛） -->
            <path d="M -7 -167 Q -4 -169 -1.5 -167 M 2 -167 Q 4.5 -169 7.5 -167" stroke="#6E6A64" stroke-width="1.3" fill="none"/>
            <path d="M -6 -151 Q 0.5 -154 7 -151" stroke="#EAE4D7" stroke-width="2.1" fill="none" stroke-linecap="round"/>
            <path d="M -5.5 -148 C -7.5 -133, -3.5 -114, 0.5 -105 C 4.5 -114, 8 -133, 6 -148 Q 0.5 -145 -5.5 -148 Z" fill="#F5F0E6" stroke="#5A554E" stroke-width="1.0"/>
            <path d="M -2.5 -146 C -2.5 -132, -0.5 -119, 0.5 -110 M 2.5 -146 C 3 -132, 2 -119, 1 -111" stroke="#7A756E" stroke-width="0.8" fill="none"/>
SVG;
        }

        return <<<SVG
        <g transform="translate({$ms['posX']}, {$ms['posY']}) scale({$scaleStr})">
          <!-- 地面微墨投影 -->
          <ellipse cx="0" cy="1" rx="26" ry="4" fill="#2E2922" opacity="0.22"/>

          <!-- 褲管（著褲） -->
          <path d="M -16 -46 L -17 -14 L -4 -14 L -2 -46 Z" fill="#46525A" stroke="#1E1B18" stroke-width="1.3"/>
          <path d="M 2 -46 L 4 -14 L 17 -14 L 16 -46 Z" fill="#46525A" stroke="#1E1B18" stroke-width="1.3"/>
          <path d="M -10 -42 L -11 -16 M 10 -42 L 11 -16" stroke="#1E1B18" stroke-width="0.9" opacity="0.45"/>

          {$footSvg}

          <!-- 交領右衽寬袖長袍（傳統礦物設色 + 淡墨衣褶渲染） -->
          <path d="M -13 -139 L 13 -139 C 24 -135, 34 -121, 38 -103 C 36 -93, 27 -89, 21 -93 L 26 -39 C 9 -35, -11 -35, -26 -40 L -21 -94 C -27 -90, -35 -93, -37 -103 C -33 -121, -23 -135, -13 -139 Z"
                fill="{$cMain}" stroke="none"/>
          <path d="M -21 -94 L -26 -40 C -17 -38, -11 -38, -7 -39 L -10 -94 Z" fill="#1A1816" opacity="0.18"/>
          <path d="M 13 -139 C 24 -135, 34 -121, 38 -103 C 36 -93, 27 -89, 21 -93 L 17 -115 Z" fill="#1A1816" opacity="0.20"/>
          <path d="M 10 -88 L 22 -39 L 26 -39 L 21 -93 Z" fill="{$cDark}" opacity="0.45"/>

          <!-- 蘭葉描衣紋毛筆勾勒（頓筆、行筆、出鋒） -->
          <g filter="url(#brush-bleed-{$uid})">
            <path d="M 13 -139 C 25 -135, 35 -120, 39 -103 C 37 -92, 27 -88, 21 -93 C 27 -91, 34 -95, 36 -103 C 32 -120, 23 -133, 12 -138 Z" fill="#1E1B18"/>
            <path d="M -13 -139 C -25 -135, -34 -120, -38 -103 C -36 -92, -27 -88, -21 -94 C -27 -92, -33 -95, -35 -103 C -31 -120, -22 -133, -12 -138 Z" fill="#1E1B18"/>
            <path d="M -20 -95 L -27 -39 C -10 -35, 10 -35, 27 -39 L 21 -95 L 19 -95 L 25 -40 C 9 -37, -9 -37, -25 -40 L -19 -95 Z" fill="#1E1B18"/>
            <path d="M -11 -139 L 5 -111 L 3 -110 L -12 -138 Z" fill="#1E1B18"/>
            <path d="M 11 -139 L -5 -107 L -3 -106 L 12 -138 Z" fill="#1E1B18"/>
            <path d="M -33 -103 C -26 -98, -21 -102, -18 -113 C -22 -104, -27 -100, -33 -103 Z" fill="#1E1B18"/>
            <path d="M 33 -103 C 26 -98, 21 -102, 18 -113 C 22 -104, 27 -100, 33 -103 Z" fill="#1E1B18"/>
            <path d="M -14 -86 C -16 -68, -18 -52, -20 -40 C -17 -52, -14 -68, -13 -86 Z" fill="#1E1B18"/>
            <path d="M -2 -85 C -3 -67, -4 -51, -4 -38 C -2 -51, -1 -67, -1 -85 Z" fill="#1E1B18"/>
            <path d="M 11 -86 C 13 -68, 15 -52, 17 -40 C 14 -52, 12 -68, 10 -86 Z" fill="#1E1B18"/>
          </g>

          <!-- 腰帶與飄帶 -->
          <path d="M -17 -96 Q 0 -92 17 -96 L 18 -87 Q 0 -83 -18 -87 Z" fill="{$bMain}" stroke="#1E1B18" stroke-width="1.4"/>
          <path d="M -5 -87 C -9 -72, -13 -56, -15 -43 L -9 -46 C -7 -58, -4 -72, -1 -86 Z" fill="{$bMain}" stroke="#1E1B18" stroke-width="1.2"/>
          <path d="M 2 -86 C 4 -71, 8 -55, 11 -42 L 5 -45 C 3 -57, 1 -71, -1 -86 Z" fill="{$bMain}" stroke="#1E1B18" stroke-width="1.2"/>

          <!-- 雙手（白描細線） -->
          <path d="M -26 -96 C -30 -93, -29 -87, -24 -87 C -20 -87, -19 -92, -21 -96 Z" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.2"/>
          <path d="M 26 -96 C 30 -93, 29 -87, 24 -87 C 20 -87, 19 -92, 21 -96 Z" fill="#EBD0B3" stroke="#1E1B18" stroke-width="1.2"/>

          <!-- 頸部與頭部（工筆白描人物，微向左側視向耕牛） -->
          <path d="M -5.5 -145 L -5.5 -136 Q 0 -132 5.5 -136 L 5.5 -145 Z" fill="#E4C3A1" stroke="#1E1B18" stroke-width="1.1"/>
          <path d="M -12 -166 C -13 -179, 13 -179, 12 -166 Z" fill="#23201E"/>
          <!-- 平梳兩髻在頂真上 -->
          <path d="M -9 -177 C -14 -181, -13 -189, -7.5 -189 C -3.5 -189, -2.5 -182, -5.5 -177 Z" fill="#23201E" stroke="#1E1B18" stroke-width="1.2"/>
          <path d="M 9 -177 C 14 -181, 13 -189, 7.5 -189 C 3.5 -189, 2.5 -182, 5.5 -177 Z" fill="#23201E" stroke="#1E1B18" stroke-width="1.2"/>
          <path d="M -11 -178 L -4.5 -177 M 4.5 -177 L 11 -178" stroke="#B5382A" stroke-width="1.7" stroke-linecap="round"/>

          <!-- 鵝蛋臉廓 -->
          <path d="M -12 -167 C -12 -153, -6.5 -144, 0 -144 C 6.5 -144, 12 -153, 12 -167 C 12 -174, -12 -174, -12 -167 Z"
                fill="#F2D5B6" stroke="#1E1B18" stroke-width="1.4"/>
          <ellipse cx="-6.5" cy="-157" rx="3.0" ry="2.0" fill="#C96B52" opacity="0.22"/>
          <ellipse cx="6.5" cy="-157" rx="3.0" ry="2.0" fill="#C96B52" opacity="0.22"/>

          {$earSvg}

          <!-- 工筆五官 -->
          <path d="M -9 -165 Q -5.5 -168 -2 -165.5 M 1.5 -165.5 Q 5 -168 8.5 -165" stroke="#1E1B18" stroke-width="1.25" fill="none" stroke-linecap="round"/>
          <path d="M -8.5 -161.5 Q -5.5 -163.3 -2.5 -161.5 Q -5.5 -160.2 -8.5 -161.5 Z" fill="#1E1B18"/>
          <path d="M 2 -161.5 Q 5 -163.3 8 -161.5 Q 5 -160.2 2 -161.5 Z" fill="#1E1B18"/>
          <path d="M -0.5 -162 L -1.8 -154.5 L 0.8 -154.2" stroke="#1E1B18" stroke-width="1.05" fill="none" stroke-linecap="round"/>
          <path d="M -3.5 -149.5 Q -0.5 -148.2 2.8 -149.5" stroke="#9E2A22" stroke-width="1.35" fill="none" stroke-linecap="round"/>

          {$ageSvg}

          <!-- 柳枝鞭杖與五色絲結（青、紅、黃、白、黑） -->
          <g filter="url(#brush-bleed-{$uid})">
            <path d="M 21 -83 C 30 -108, 42 -138, 55 -166" stroke="#5A3C22" stroke-width="2.0" fill="none" stroke-linecap="round"/>
            <path d="M 37 -124 C 45 -130, 53 -128, 56 -124 C 49 -122, 42 -122, 37 -124 Z" fill="#466935"/>
            <path d="M 45 -144 C 37 -150, 30 -148, 27 -144 C 34 -142, 40 -142, 45 -144 Z" fill="#466935"/>
            <path d="M 50 -156 C 57 -161, 63 -159, 66 -156 C 59 -154, 54 -154, 50 -156 Z" fill="#52783E"/>
            <path d="M 55 -166 C 63 -159, 65 -147, 60 -138" stroke="#2C634B" stroke-width="1.5" fill="none"/>
            <path d="M 55 -166 C 67 -162, 71 -150, 66 -141" stroke="#B5382A" stroke-width="1.5" fill="none"/>
            <path d="M 55 -166 C 60 -155, 59 -144, 53 -136" stroke="#D19326" stroke-width="1.5" fill="none"/>
            <path d="M 55 -166 C 69 -167, 75 -157, 71 -147" stroke="#F4EFE4" stroke-width="1.5" fill="none"/>
            <path d="M 55 -166 C 57 -154, 53 -144, 48 -137" stroke="#23201E" stroke-width="1.4" fill="none"/>
            <circle cx="55" cy="-166" r="2.5" fill="#B5382A" stroke="#1E1B18" stroke-width="0.9"/>
          </g>
        </g>
SVG;
    }
}

if (!function_exists('renderMangshenSvg')) {
    /**
     * 渲染完整傳統中國水墨春牛圖 SVG
     *
     * @param array  $oxData 由 parseSpringOx() 回傳的結構化陣列
     * @param string $gzYear 歲次干支（如「丙午」），若留空則自動讀取全域 $ly / $ganZhiYear
     * @return string 完整 SVG HTML 字串
     */
    function renderMangshenSvg($oxData, $gzYear = '') {
        $ox  = $oxData['ox'];
        $ms  = $oxData['mangshen'];
        $uid = substr(md5(uniqid('', true)), 0, 6);

        // 取得歲次干支文字（用於右側年款直式牌）
        if ($gzYear === '') {
            if (!empty($GLOBALS['ly'])) {
                $gzYear = (string) $GLOBALS['ly'];
            } elseif (!empty($GLOBALS['ganZhiYear'])) {
                $gzYear = (string) $GLOBALS['ganZhiYear'];
            } else {
                $gzYear = '丙午';
            }
        }
        $gzChars = preg_split('//u', $gzYear, -1, PREG_SPLIT_NO_EMPTY);
        $gz1 = htmlspecialchars($gzChars[0] ?? '丙', ENT_QUOTES, 'UTF-8');
        $gz2 = htmlspecialchars($gzChars[1] ?? '午', ENT_QUOTES, 'UTF-8');

        $bodyD  = springOxAdjustHex($ox['body'], 0.72);
        $headD  = springOxAdjustHex($ox['head'], 0.72);
        $bellyD = springOxAdjustHex($ox['belly'], 0.74);
        $legD   = springOxAdjustHex($ox['leg'], 0.78);

        // 牛口開 / 牛口合
        if ($ox['mouthOpen']) {
            $mouthSvg = <<<SVG
            <path d="M 222 224 Q 233 226 244 223 Q 241 236 229 236 Q 222 234 222 224 Z" fill="#4A1915" stroke="#1E1B18" stroke-width="1.5"/>
            <path d="M 227 230 Q 234 227 240 231 Q 235 235 228 234 Z" fill="#C45C52"/>
            <path d="M 222 234 Q 233 239 245 233" stroke="#1E1B18" stroke-width="1.9" fill="none" stroke-linecap="round"/>
SVG;
        } else {
            $mouthSvg = <<<SVG
            <path d="M 221 225 Q 233 228 245 224" stroke="#1E1B18" stroke-width="2.1" fill="none" stroke-linecap="round"/>
            <path d="M 224 229 Q 233 233 242 229" stroke="#1E1B18" stroke-width="1.1" fill="none" opacity="0.65"/>
SVG;
        }

        // 牛尾左繳 / 牛尾右繳
        if ($ox['tailDir'] === 'left') {
            $tailFarSvg = '';
            $tailNearSvg = <<<SVG
            <!-- 牛尾左繳（向牛身左側盤繞垂落，水墨飛白尾帚） -->
            <g filter="url(#brush-bleed-{$uid})">
              <path d="M 472 188 C 483 206, 475 236, 452 258" stroke="#1E1B18" stroke-width="6.2" fill="none" stroke-linecap="round"/>
              <path d="M 472 188 C 483 206, 475 236, 452 258" stroke="{$ox['tail']}" stroke-width="3.6" fill="none" stroke-linecap="round"/>
              <path d="M 453 256 C 441 262, 434 274, 436 284 C 443 280, 449 275, 454 267 C 452 275, 448 283, 445 289 C 453 283, 459 273, 459 260 Z"
                    fill="{$ox['tail']}" stroke="#1E1B18" stroke-width="1.5" stroke-linejoin="round"/>
              <path d="M 451 260 C 444 269, 440 277, 439 285 M 455 262 C 451 271, 448 279, 447 286" stroke="#1E1B18" stroke-width="1.1" fill="none"/>
            </g>
SVG;
        } else {
            $tailNearSvg = '';
            $tailFarSvg = <<<SVG
            <!-- 牛尾右繳（向外右側揚起垂落，水墨飛白尾帚） -->
            <g filter="url(#brush-bleed-{$uid})">
              <path d="M 472 188 C 500 198, 512 238, 500 268" stroke="#1E1B18" stroke-width="6.2" fill="none" stroke-linecap="round"/>
              <path d="M 472 188 C 500 198, 512 238, 500 268" stroke="{$ox['tail']}" stroke-width="3.6" fill="none" stroke-linecap="round"/>
              <path d="M 500 266 C 493 276, 493 288, 500 296 C 505 290, 508 282, 506 274 C 509 282, 511 289, 511 294 C 516 286, 513 274, 505 266 Z"
                    fill="{$ox['tail']}" stroke="#1E1B18" stroke-width="1.5" stroke-linejoin="round"/>
              <path d="M 502 269 C 499 278, 499 286, 501 293 M 505 270 C 506 278, 508 285, 509 291" stroke="#1E1B18" stroke-width="1.1" fill="none"/>
            </g>
SVG;
        }

        // 桑柘木籠頭與苧繩結（立於牛左前時自然牽引至芒神手中，其餘位置自然垂掛）
        if ($ms['posKey'] === 'front_left') {
            $ropeSvg = <<<SVG
            <path d="M 243 184 L 232 214 M 266 180 L 241 200" stroke="#4E3520" stroke-width="1.8" fill="none"/>
            <circle cx="222" cy="222" r="4.2" fill="none" stroke="#9C7228" stroke-width="1.9"/>
            <path d="M 218 224 C 198 254, 174 266, 156 256" stroke="{$ox['rope']}" stroke-width="2.4" fill="none" stroke-linecap="round" filter="url(#brush-bleed-{$uid})"/>
            <g transform="translate(188, 255)" stroke="#1E1B18" stroke-width="1.0">
              <circle cx="-3" cy="0" r="3.4" fill="{$ox['rope']}"/>
              <circle cx="3" cy="0" r="3.4" fill="{$ox['rope']}"/>
              <circle cx="0" cy="-3" r="3.0" fill="{$ox['rope']}"/>
              <path d="M -2 3 L -5 11 M 2 3 L 5 11" stroke="{$ox['rope']}" stroke-width="1.8" stroke-linecap="round"/>
            </g>
SVG;
        } else {
            $ropeSvg = <<<SVG
            <path d="M 243 184 L 232 214 M 266 180 L 241 200" stroke="#4E3520" stroke-width="1.8" fill="none"/>
            <circle cx="222" cy="222" r="4.2" fill="none" stroke="#9C7228" stroke-width="1.9"/>
            <path d="M 218 224 C 204 252, 224 276, 256 262 C 266 257, 272 246, 270 236" stroke="{$ox['rope']}" stroke-width="2.4" fill="none" stroke-linecap="round" filter="url(#brush-bleed-{$uid})"/>
            <g transform="translate(226, 265)" stroke="#1E1B18" stroke-width="1.0">
              <circle cx="-3" cy="0" r="3.4" fill="{$ox['rope']}"/>
              <circle cx="3" cy="0" r="3.4" fill="{$ox['rope']}"/>
              <circle cx="0" cy="-3" r="3.0" fill="{$ox['rope']}"/>
              <path d="M -2 3 L -4 11 M 2 3 L 4 11" stroke="{$ox['rope']}" stroke-width="1.8" stroke-linecap="round"/>
            </g>
SVG;
        }

        // 芒神前後景深遮擋排序（牛右在春牛後方，牛左在春牛前景）
        $mangshenSvg  = renderMangshenFigureSvg($oxData, $uid);
        $mangshenFar  = ($ms['side'] === 'right') ? $mangshenSvg : '';
        $mangshenNear = ($ms['side'] === 'left')  ? $mangshenSvg : '';

        return <<<SVG
<svg viewBox="0 0 680 420" width="680" height="420" style="max-width:100%;height:auto;display:block;margin:0 auto;" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{$gz1}{$gz2}年傳統水墨春牛圖">
  <defs>
    <!-- 水墨暈染濾鏡（遠山雲霧與積墨渲染） -->
    <filter id="ink-wash-soft-{$uid}" x="-15%" y="-15%" width="130%" height="130%">
      <feTurbulence type="fractalNoise" baseFrequency="0.03" numOctaves="3" seed="8" result="washNoise"/>
      <feDisplacementMap in="SourceGraphic" in2="washNoise" scale="4.5" xChannelSelector="R" yChannelSelector="G" result="displaced"/>
      <feGaussianBlur in="displaced" stdDeviation="3.8"/>
    </filter>
    <!-- 毛筆筆鋒與宣紙微滲濾鏡 -->
    <filter id="brush-bleed-{$uid}" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.075" numOctaves="3" seed="3" result="edgeNoise"/>
      <feDisplacementMap in="SourceGraphic" in2="edgeNoise" scale="1.35" xChannelSelector="R" yChannelSelector="G"/>
    </filter>
  </defs>

  <!-- ==================== 1. 舊宣紙底層與歲月包漿 ==================== -->
  <rect x="0" y="0" width="680" height="420" fill="#F2E6CE"/>
  <rect x="10" y="10" width="660" height="400" fill="none" stroke="#DFCCA8" stroke-width="16" opacity="0.55"/>
  <g stroke="#8C7352" stroke-width="0.45" opacity="0.18">
    <path d="M 38 42 Q 52 44 64 40 M 142 28 Q 158 31 172 27 M 312 36 Q 328 33 344 37 M 488 44 Q 505 41 518 45"/>
    <path d="M 85 195 Q 102 192 118 196 M 532 282 Q 548 285 565 280 M 112 386 Q 130 383 148 387 M 422 392 Q 440 389 458 393"/>
  </g>

  <!-- ==================== 2. 水墨山水遠景與自然煙嵐雲霧 ==================== -->
  <g filter="url(#ink-wash-soft-{$uid})">
    <path d="M 14 218 C 65 154, 118 128, 168 156 C 215 182, 265 130, 322 120 C 378 112, 422 162, 476 142 C 525 128, 578 156, 666 196 L 666 250 L 14 250 Z"
          fill="#6B655C" opacity="0.14"/>
    <path d="M 14 242 C 78 190, 138 176, 198 206 C 258 232, 315 178, 376 172 C 435 166, 488 208, 548 188 C 595 174, 632 192, 666 214 L 666 270 L 14 270 Z"
          fill="#524D45" opacity="0.16"/>
    <!-- 山谷流雲留白帶 -->
    <path d="M 24 224 C 85 202, 155 215, 225 222 C 295 229, 365 198, 445 212 C 525 224, 595 205, 658 218 C 615 245, 515 248, 415 242 C 305 236, 185 252, 24 244 Z"
          fill="#F2E6CE" opacity="0.62"/>
  </g>

  <!-- 傳統水墨祥雲 -->
  <g fill="none" stroke="#7A6D5B" stroke-width="1.2" opacity="0.48" stroke-linecap="round">
    <path d="M 195 74 C 208 64, 228 65, 236 75 C 248 69, 264 73, 268 82 C 250 85, 218 85, 196 82"/>
    <path d="M 214 89 C 234 86, 262 86, 284 91"/>
    <path d="M 455 58 C 468 49, 486 50, 494 59 C 505 54, 520 57, 524 65 C 506 68, 476 68, 456 65"/>
  </g>

  <!-- 左上角早春垂柳（寫意墨枝與蘭葉撇柳葉） -->
  <g filter="url(#brush-bleed-{$uid})" opacity="0.86">
    <path d="M 14 34 C 42 46, 76 54, 116 48" stroke="#2B2622" stroke-width="3.0" fill="none" stroke-linecap="round"/>
    <path d="M 54 48 C 78 36, 106 32, 136 36" stroke="#3A342E" stroke-width="1.9" fill="none" stroke-linecap="round"/>
    <path d="M 44 45 C 40 74, 34 104, 28 134" stroke="#4D4840" stroke-width="1.15" fill="none"/>
    <path d="M 68 49 C 66 80, 62 112, 56 144" stroke="#4D4840" stroke-width="1.15" fill="none"/>
    <path d="M 94 49 C 94 78, 91 106, 86 132" stroke="#5E584E" stroke-width="1.05" fill="none"/>
    <path d="M 118 35 C 120 62, 118 88, 114 112" stroke="#6E685D" stroke-width="0.95" fill="none"/>
    <g fill="#3E4A36" opacity="0.82">
      <path d="M 41 64 C 35 69, 32 75, 31 80 C 36 76, 39 70, 41 64 Z"/>
      <path d="M 38 86 C 43 92, 45 98, 45 103 C 41 98, 39 92, 38 86 Z"/>
      <path d="M 33 108 C 27 113, 24 119, 23 124 C 28 120, 31 114, 33 108 Z"/>
      <path d="M 66 70 C 60 75, 57 81, 56 86 C 61 82, 64 76, 66 70 Z"/>
      <path d="M 64 94 C 69 100, 71 106, 71 111 C 67 106, 65 100, 64 94 Z"/>
      <path d="M 60 116 C 54 121, 51 127, 50 132 C 55 128, 58 122, 60 116 Z"/>
      <path d="M 93 68 C 88 73, 85 79, 84 84 C 89 80, 91 74, 93 68 Z"/>
      <path d="M 91 92 C 96 98, 98 103, 98 108 C 94 103, 92 98, 91 92 Z"/>
      <path d="M 118 58 C 113 63, 110 68, 109 73 C 114 69, 116 64, 118 58 Z"/>
    </g>
  </g>

  <!-- ==================== 3. 近景土坡、皴線與點苔 ==================== -->
  <g filter="url(#ink-wash-soft-{$uid})">
    <path d="M 14 334 C 145 322, 290 330, 445 326 C 540 324, 610 330, 666 336 L 666 406 L 14 406 Z"
          fill="#8A7659" opacity="0.20"/>
    <path d="M 45 352 C 185 344, 360 356, 520 348 C 585 345, 635 352, 666 358 L 666 406 L 14 406 L 14 360 Z"
          fill="#4A4339" opacity="0.16"/>
  </g>

  <g filter="url(#brush-bleed-{$uid})" fill="none" stroke-linecap="round">
    <path d="M 28 344 C 95 338, 165 341, 224 345" stroke="#4D4438" stroke-width="1.5" opacity="0.58"/>
    <path d="M 485 346 C 545 341, 605 343, 652 348" stroke="#4D4438" stroke-width="1.5" opacity="0.58"/>
    <path d="M 180 368 C 265 362, 365 365, 448 370" stroke="#5C5142" stroke-width="1.2" opacity="0.48"/>

    <!-- 水墨蘭葉草叢與點苔 -->
    <path d="M 62 356 C 56 344, 48 336, 41 332" stroke="#2D3324" stroke-width="1.7"/>
    <path d="M 65 356 C 63 341, 62 330, 64 323" stroke="#2D3324" stroke-width="1.6"/>
    <path d="M 68 356 C 74 344, 82 337, 89 334" stroke="#3D4532" stroke-width="1.4"/>
    <path d="M 72 357 C 81 350, 89 347, 96 346" stroke="#4D5440" stroke-width="1.2"/>
    <circle cx="52" cy="354" r="1.7" fill="#23201E" stroke="none"/>
    <circle cx="58" cy="358" r="1.3" fill="#23201E" stroke="none"/>
    <circle cx="77" cy="358" r="1.5" fill="#23201E" stroke="none"/>

    <path d="M 256 355 C 252 346, 247 340, 242 337" stroke="#3A4030" stroke-width="1.3"/>
    <path d="M 259 355 C 260 345, 262 338, 265 334" stroke="#3A4030" stroke-width="1.3"/>
    <path d="M 262 355 C 268 348, 274 344, 279 343" stroke="#4E5442" stroke-width="1.1"/>

    <path d="M 596 358 C 590 346, 583 338, 576 334" stroke="#2D3324" stroke-width="1.6"/>
    <path d="M 600 358 C 600 343, 601 332, 604 325" stroke="#2D3324" stroke-width="1.5"/>
    <path d="M 604 358 C 611 347, 619 341, 626 338" stroke="#3D4532" stroke-width="1.4"/>
    <circle cx="588" cy="358" r="1.6" fill="#23201E" stroke="none"/>
    <circle cx="612" cy="359" r="1.4" fill="#23201E" stroke="none"/>
  </g>

  <!-- ==================== 4. 遠側芒神（當立於牛右邊時，位於春牛後方） ==================== -->
  {$mangshenFar}

  <!-- ==================== 5. 傳統水墨春牛（主體） ==================== -->
  <g id="spring-ox-main">
    <ellipse cx="378" cy="347" rx="115" ry="9" fill="#2B2620" opacity="0.22" filter="url(#ink-wash-soft-{$uid})"/>

    {$tailFarSvg}

    <!-- 5.1 遠側雙肢 -->
    <path d="M 291 250 L 288 282 C 287 290, 287 296, 289 302 L 286 333 L 298 333 L 301 301 C 303 293, 304 282, 306 252 Z"
          fill="{$ox['leg']}" stroke="#1E1B18" stroke-width="1.6" stroke-linejoin="round" filter="url(#brush-bleed-{$uid})"/>
    <path d="M 291 250 L 288 282 C 287 290, 287 296, 289 302 L 286 333 L 298 333 L 301 301 C 303 293, 304 282, 306 252 Z"
          fill="#1A1816" opacity="0.24"/>
    <path d="M 286 333 L 281 344 L 291 344 L 292 339 L 294 344 L 300 344 L 298 333 Z"
          fill="{$ox['hoof']}" stroke="#1E1B18" stroke-width="1.5" stroke-linejoin="round"/>

    <path d="M 430 248 L 434 278 C 436 286, 437 293, 434 301 L 429 333 L 441 333 L 446 300 C 449 291, 449 280, 446 250 Z"
          fill="{$ox['leg']}" stroke="#1E1B18" stroke-width="1.6" stroke-linejoin="round" filter="url(#brush-bleed-{$uid})"/>
    <path d="M 430 248 L 434 278 C 436 286, 437 293, 434 301 L 429 333 L 441 333 L 446 300 C 449 291, 449 280, 446 250 Z"
          fill="#1A1816" opacity="0.24"/>
    <path d="M 429 333 L 424 344 L 434 344 L 435 339 L 437 344 L 443 344 L 441 333 Z"
          fill="{$ox['hoof']}" stroke="#1E1B18" stroke-width="1.5" stroke-linejoin="round"/>

    <!-- 5.2 牛身主軀幹（肩峰、脊背、髖骨、臀部與上下肢肌肉自然一體） -->
    <path d="M 272 178
             C 284 176, 294 175, 304 168
             C 316 159, 332 158, 346 167
             C 366 175, 396 175, 424 171
             C 442 169, 458 173, 470 185
             C 482 198, 484 222, 476 244
             C 474 252, 471 258, 467 262
             L 447 260
             C 443 255, 440 250, 435 248
             C 404 265, 355 267, 326 252
             L 325 262 L 309 262
             C 308 256, 307 252, 304 250
             C 291 252, 281 244, 274 234
             C 264 222, 264 194, 272 178 Z"
          fill="{$ox['body']}" stroke="none"/>

    <!-- 5.3 牛腹 -->
    <path d="M 326 238
             C 358 246, 404 245, 434 234
             C 430 252, 404 264, 378 264
             C 352 264, 331 254, 326 238 Z"
          fill="{$ox['belly']}" stroke="none"/>
    <path d="M 329 246 C 358 256, 402 256, 430 242 C 425 256, 402 264, 378 264 C 354 264, 334 256, 329 246 Z"
          fill="{$bellyD}" opacity="0.55"/>

    <!-- 5.4 水墨積墨與留白立體渲染 -->
    <g filter="url(#ink-wash-soft-{$uid})">
      <path d="M 314 164 C 330 160, 348 167, 366 175 C 346 177, 326 174, 312 169 Z" fill="#FAF4E6" opacity="0.28"/>
      <path d="M 384 176 C 408 175, 432 173, 452 177 C 430 182, 405 182, 384 179 Z" fill="#FAF4E6" opacity="0.24"/>
      <path d="M 336 176 C 346 196, 346 220, 336 237 C 358 241, 388 239, 410 230 C 398 212, 394 192, 396 177 Z" fill="#1A1816" opacity="0.16"/>
      <path d="M 424 180 C 428 202, 428 222, 422 238 C 438 242, 456 244, 470 234 C 468 212, 460 190, 446 178 Z" fill="#1A1816" opacity="0.18"/>
      <path d="M 272 216 C 284 234, 302 244, 318 246 C 312 254, 294 252, 276 236 Z" fill="#1A1816" opacity="0.22"/>
      <path d="M 464 186 C 476 202, 478 228, 468 256 L 474 252 C 483 226, 481 198, 468 184 Z" fill="{$bodyD}" opacity="0.65"/>
    </g>

    <!-- 5.5 乾筆飛白與皮毛皴擦紋理 -->
    <g fill="none" stroke="#1E1B18" stroke-linecap="round" filter="url(#brush-bleed-{$uid})">
      <path d="M 282 196 C 279 210, 281 224, 288 235" stroke-width="1.4" opacity="0.58"/>
      <path d="M 292 194 C 289 210, 292 226, 299 238" stroke-width="1.2" opacity="0.48"/>
      <path d="M 302 196 C 300 210, 303 224, 309 234" stroke-width="1.0" opacity="0.38"/>
      <path d="M 332 182 C 338 202, 336 226, 327 248" stroke-width="1.5" opacity="0.52" stroke-dasharray="18 3 8 2"/>
      <path d="M 310 222 C 308 234, 308 245, 310 256" stroke-width="1.4" opacity="0.55"/>
      <path d="M 358 194 C 356 208, 357 220, 360 229" stroke-width="1.15" opacity="0.35" stroke-dasharray="10 4 5 3"/>
      <path d="M 372 195 C 370 209, 371 221, 374 230" stroke-width="1.15" opacity="0.35" stroke-dasharray="12 3 4 3"/>
      <path d="M 386 195 C 384 208, 385 219, 388 228" stroke-width="1.05" opacity="0.30" stroke-dasharray="9 4"/>
      <path d="M 328 239 C 360 246, 402 245, 431 235" stroke-width="1.4" opacity="0.65" stroke-dasharray="28 3 14 2"/>
      <path d="M 442 188 C 434 208, 434 230, 444 252" stroke-width="1.5" opacity="0.52" stroke-dasharray="18 3 8 2"/>
    </g>

    <!-- 5.6 牛身毛筆輪廓線（具頓筆、行筆、出鋒之粗細變化筆鋒） -->
    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 274 177 C 285 175, 295 174, 304 167 C 316 158, 333 157, 347 166 C 367 174, 397 174, 425 170 C 443 168, 460 172, 472 184 C 483 197, 485 222, 477 244 C 475 251, 472 256, 468 261 L 466 259 C 470 254, 472 249, 474 243 C 482 222, 480 199, 469 186 C 458 175, 442 171, 425 173 C 397 177, 366 177, 346 169 C 333 160, 317 161, 306 169 C 296 177, 285 178, 274 179 Z"
            fill="#1E1B18"/>
      <path d="M 274 234 C 285 246, 298 251, 308 250" stroke="#1E1B18" stroke-width="2.1" fill="none" stroke-linecap="round"/>
      <path d="M 327 253 C 356 266, 401 266, 433 250" stroke="#1E1B18" stroke-width="2.2" fill="none" stroke-linecap="round"/>
    </g>

    <!-- 5.7 近側雙肢（牛脛與偶蹄） -->
    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 310 258 C 311 270, 313 280, 315 288 C 314 295, 315 302, 317 309 L 314 335 L 327 335 L 326 307 C 328 299, 329 291, 327 283 C 327 274, 326 266, 325 258 Z"
            fill="{$ox['leg']}" stroke="none"/>
      <path d="M 321 258 C 322 278, 322 306, 321 335 L 327 335 L 326 307 C 328 299, 329 291, 327 283 C 327 274, 326 266, 325 258 Z"
            fill="{$legD}" opacity="0.35"/>
      <path d="M 309 257 Q 317 262 326 257" stroke="#1E1B18" stroke-width="1.3" fill="none" opacity="0.55" stroke-dasharray="6 2 8 2"/>
      <path d="M 310 256 C 311 268, 313 279, 315 288 C 314 295, 315 302, 317 309 L 314 335 M 325 256 C 326 266, 327 275, 327 283 C 329 291, 328 299, 326 307 L 327 335"
            stroke="#1E1B18" stroke-width="1.9" fill="none" stroke-linecap="round"/>
      <path d="M 320 286 L 319 332" stroke="#1E1B18" stroke-width="1.0" opacity="0.45"/>
      <path d="M 314 335 L 308 347 L 319 347 L 321 341 L 323 347 L 330 347 L 327 335 Z"
            fill="{$ox['hoof']}" stroke="#1E1B18" stroke-width="1.8" stroke-linejoin="round"/>
    </g>

    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 446 256 C 449 268, 453 278, 456 287 C 458 294, 457 301, 455 309 L 451 335 L 464 335 L 467 307 C 470 297, 472 288, 469 279 C 469 271, 468 264, 467 256 Z"
            fill="{$ox['leg']}" stroke="none"/>
      <path d="M 459 256 C 462 278, 461 306, 458 335 L 464 335 L 467 307 C 470 297, 472 288, 469 279 C 469 271, 468 264, 467 256 Z"
            fill="{$legD}" opacity="0.35"/>
      <path d="M 445 255 Q 456 261 468 256" stroke="#1E1B18" stroke-width="1.3" fill="none" opacity="0.55" stroke-dasharray="7 2 8 2"/>
      <path d="M 445 254 C 449 267, 453 278, 456 287 C 458 294, 457 301, 455 309 L 451 335 M 468 254 C 469 264, 469 272, 469 279 C 472 288, 470 297, 467 307 L 464 335"
            stroke="#1E1B18" stroke-width="1.9" fill="none" stroke-linecap="round"/>
      <path d="M 460 286 L 457 332" stroke="#1E1B18" stroke-width="1.0" opacity="0.45"/>
      <path d="M 451 335 L 445 348 L 456 348 L 458 342 L 460 348 L 467 348 L 464 335 Z"
            fill="{$ox['hoof']}" stroke="#1E1B18" stroke-width="1.8" stroke-linejoin="round"/>
    </g>

    {$tailNearSvg}

    <!-- 5.8 牛角、牛頭、牛耳與五官 -->
    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 263 177 C 250 162, 243 144, 246 132 C 251 143, 259 156, 271 172 Z"
            fill="{$ox['horn']}" stroke="#1E1B18" stroke-width="1.8" stroke-linejoin="round"/>
      <path d="M 255 162 Q 259 159 262 162 M 251 152 Q 254 149 257 152" stroke="#1E1B18" stroke-width="1.0" fill="none" opacity="0.6"/>
    </g>

    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 268 172
               C 278 172, 286 180, 286 194
               C 286 212, 274 228, 256 234
               C 244 238, 230 237, 223 230
               C 218 224, 220 214, 228 206
               C 238 196, 248 184, 256 176
               C 260 172, 264 172, 268 172 Z"
            fill="{$ox['head']}" stroke="#1E1B18" stroke-width="2.2" stroke-linejoin="round"/>
      <path d="M 258 179 C 248 189, 238 199, 231 208 C 237 207, 246 198, 258 186 Z" fill="#FAF4E6" opacity="0.26" stroke="none"/>
      <path d="M 252 214 C 264 212, 276 204, 282 194 C 282 212, 270 226, 254 232 C 246 234, 240 231, 242 224 Z" fill="{$headD}" opacity="0.45" stroke="none"/>
    </g>

    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 276 176 C 272 155, 277 136, 289 128 C 283 141, 282 157, 286 175 Z"
            fill="{$ox['horn']}" stroke="#1E1B18" stroke-width="1.9" stroke-linejoin="round"/>
      <path d="M 276 165 Q 281 163 284 166 M 276 155 Q 280 153 283 156 M 278 145 Q 281 144 284 146" stroke="#1E1B18" stroke-width="1.1" fill="none" opacity="0.65"/>
    </g>

    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 284 182 C 296 175, 310 177, 314 184 C 306 191, 294 192, 284 188 Z"
            fill="{$ox['ear']}" stroke="#1E1B18" stroke-width="1.8" stroke-linejoin="round"/>
      <path d="M 288 184 Q 298 182 307 184" stroke="#1E1B18" stroke-width="1.0" fill="none" opacity="0.65"/>
    </g>

    <g filter="url(#brush-bleed-{$uid})">
      <path d="M 244 192 Q 253 187 261 192" stroke="#1E1B18" stroke-width="2.0" fill="none" stroke-linecap="round"/>
      <path d="M 246 197 Q 253 193 259 197 Q 253 201 246 197 Z" fill="#F7F2E6" stroke="#1E1B18" stroke-width="1.5"/>
      <ellipse cx="252" cy="197" rx="3.0" ry="2.4" fill="#181614"/>
      <circle cx="251" cy="196.2" r="0.8" fill="#FAF6EE"/>
      <path d="M 224 214 C 226 211, 230 212, 229 216 C 228 218, 224 217, 224 214 Z" fill="#181614"/>
      <path d="M 233 217 C 235 215, 238 216, 237 219 C 236 221, 233 220, 233 217 Z" fill="#181614"/>
      {$mouthSvg}
    </g>

    {$ropeSvg}
  </g>

  <!-- ==================== 6. 近側芒神（當立於牛左邊時，位於前景） ==================== -->
  {$mangshenNear}

  <!-- ==================== 7. 傳統年畫年款直式牌、朱文白文印章與古典邊框 ==================== -->
  <g transform="translate(604, 24)">
    <rect x="0" y="0" width="44" height="156" fill="#FBF5E6" fill-opacity="0.92" stroke="#9E2A22" stroke-width="2.0"/>
    <rect x="3.5" y="3.5" width="37" height="149" fill="none" stroke="#9E2A22" stroke-width="0.8"/>
    <text x="22" y="25" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#231D18">{$gz1}</text>
    <text x="22" y="49" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#231D18">{$gz2}</text>
    <text x="22" y="73" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#231D18">年</text>
    <text x="22" y="98" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#9E2A22">春</text>
    <text x="22" y="122" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#9E2A22">牛</text>
    <text x="22" y="146" text-anchor="middle" font-family="'Noto Serif TC', 'Songti TC', 'PMingLiU', serif" font-size="17" font-weight="bold" fill="#9E2A22">圖</text>

    <!-- 傳統朱文方印「迎春」 -->
    <g transform="translate(8, 166)">
      <rect x="0" y="0" width="28" height="28" rx="1.5" fill="none" stroke="#B52B21" stroke-width="2.0"/>
      <text x="14" y="12.5" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="10.5" font-weight="bold" fill="#B52B21">迎</text>
      <text x="14" y="24.5" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="10.5" font-weight="bold" fill="#B52B21">春</text>
    </g>

    <!-- 傳統白文方印「大吉」 -->
    <g transform="translate(8, 200)">
      <rect x="0" y="0" width="28" height="28" rx="1.5" fill="#B52B21"/>
      <text x="14" y="12.5" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="10.5" font-weight="bold" fill="#FAF3E3">大</text>
      <text x="14" y="24.5" text-anchor="middle" font-family="'Noto Serif TC', 'PMingLiU', serif" font-size="10.5" font-weight="bold" fill="#FAF3E3">吉</text>
    </g>
  </g>

  <!-- 傳統通書雙層朱砂邊框與古典雲紋角花 -->
  <rect x="8" y="8" width="664" height="404" fill="none" stroke="#9E2A22" stroke-width="3.2"/>
  <rect x="14" y="14" width="652" height="392" fill="none" stroke="#9E2A22" stroke-width="1.1"/>
  <g fill="none" stroke="#9E2A22" stroke-width="1.3">
    <path d="M 14 34 L 26 34 L 26 22 L 20 22 L 20 28 L 34 28 L 34 14"/>
    <path d="M 666 34 L 654 34 L 654 22 L 660 22 L 660 28 L 646 28 L 646 14"/>
    <path d="M 14 386 L 26 386 L 26 398 L 20 398 L 20 392 L 34 392 L 34 406"/>
    <path d="M 666 386 L 654 386 L 654 398 L 660 398 L 660 392 L 646 392 L 646 406"/>
  </g>
</svg>
SVG;
    }
}

// =========================================================================
// 自動執行輸出（同時支援被 springox.php / lunardate.php include 與獨立執行預覽）
// =========================================================================
$isDirectCall = (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__)
             || (PHP_SAPI === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__);

$defaultSpringOxText = '春牛身高四尺，長八尺，尾長一尺二寸，牛頭紅色，牛身紅色，牛腹黑色，牛角、耳、尾黃色，牛脛白色，牛蹄黃色，牛尾左繳。牛口開，牛籠頭拘繩桑柘木，用苧繩結青色，牛踏板縣門左扇。芒神身高三尺六寸五分，面如少壯像，紅衣繫黑腰帶，平梳兩髻在頂真上，罨耳全戴，揭起左邊，著褲，無行纏鞋子，鞭杖用柳枝，長二尺四寸，五色醮染用苧結，芒神早忙，立於牛左前邊。';

$activeOxText = isset($oxcontent) && trim((string) $oxcontent) !== ''
    ? (string) $oxcontent
    : $defaultSpringOxText;

$activeGzYear = '';
if (isset($ly) && trim((string) $ly) !== '') {
    $activeGzYear = (string) $ly;
} elseif (isset($ganZhiYear) && trim((string) $ganZhiYear) !== '') {
    $activeGzYear = (string) $ganZhiYear;
} elseif (isset($lunar) && is_object($lunar) && method_exists($lunar, 'getYearInGanZhi')) {
    $activeGzYear = (string) $lunar->getYearInGanZhi();
} else {
    $activeGzYear = '丙午';
}

$parsedOxData = parseSpringOx($activeOxText);
$springOxSvg  = renderMangshenSvg($parsedOxData, $activeGzYear);

if ($isDirectCall && PHP_SAPI !== 'cli') {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="zh-Hant"><head><meta charset="UTF-8"><title>'
       . htmlspecialchars($activeGzYear, ENT_QUOTES, 'UTF-8')
       . '年春牛圖</title><style>body{margin:0;padding:24px;background:#e8dec8;display:flex;justify-content:center;align-items:center;min-height:100vh;}</style></head><body>'
       . $springOxSvg
       . '</body></html>';
} else {
    echo $springOxSvg;
}
