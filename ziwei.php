<?php include 'metaseo.php'; ?>

<meta name="description" content="免費的紫微斗數排盤工具，提供12宮位星曜詳情、PNG下載和AI提示詞。">
<link rel="canonical" href="https://lunar.ioi.tw/ziwei.php" />
<title>紫微斗數排盤 | 農民曆</title>

<?php include 'head.php'; ?>

<link rel="stylesheet" href="ziwei-style.css?v=1.2">

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
                    <a id="calendar" class="nav-link" href="/calendar/">日曆(自動更新)</a>
                </li>
                <li class="nav-item">
                    <a id="ziwei" class="nav-link" href="ziwei.php">紫微斗數排盤</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <h1 class="text-center" style="margin-top: 20px;">紫微斗數線上排盤</h1>

            <?php
                // *** 步驟 1：新增 PHP 程式碼來獲取當前時間 ***
                
                // 確保時區設定正確 (與你的 yuangang.php 相同)
                date_default_timezone_set('Asia/Taipei');
                
                // 獲取當前日期 (格式: YYYY-MM-DD)
                $currentDate = date('Y-m-d');
                
                // 獲取當前小時 (格式: 0-23)
                $currentHour = (int)date('G');
                
                // 根據小時計算 iztro 的時辰索引 (0-11)
                // 0 = 子時 (23-1), 1 = 丑時 (1-3), ... 11 = 亥時 (21-23)
                if ($currentHour == 23) {
                    $currentTimeIndex = 0;
                } else {
                    // 公式：(小時+1) / 2，然後取整數
                    $currentTimeIndex = floor(($currentHour + 1) / 2);
                }
            ?>

            <div class="card card-body bg-light mb-4">
                <form class="form-inline" style="justify-content: center;">
            
                    <label class="mr-2 mb-2" for="name">【姓名】</label>
                    <input type="text" id="name" class="form-control mr-3 mb-2" placeholder="請輸入姓名" style="width: 150px;">
                    
                    <label class="mr-2 mb-2" for="birthDate">【陽曆生日】</label>
                    <input type="date" id="birthDate" class="form-control mr-3 mb-2" value="<?php echo $currentDate; ?>">
                    
                    <label class="mr-2 mb-2" for="birthTime">【出生時辰】</label>
                    <select id="birthTime" class="form-control mr-3 mb-2">
                        <option value="0" <?php if ($currentTimeIndex == 0) echo 'selected'; ?>>子時 (23:00 - 00:59)</option>
                        <option value="1" <?php if ($currentTimeIndex == 1) echo 'selected'; ?>>丑時 (01:00 - 02:59)</option>
                        <option value="2" <?php if ($currentTimeIndex == 2) echo 'selected'; ?>>寅時 (03:00 - 04:59)</option>
                        <option value="3" <?php if ($currentTimeIndex == 3) echo 'selected'; ?>>卯時 (05:00 - 06:59)</option>
                        <option value="4" <?php if ($currentTimeIndex == 4) echo 'selected'; ?>>辰時 (07:00 - 08:59)</option>
                        <option value="5" <?php if ($currentTimeIndex == 5) echo 'selected'; ?>>巳時 (09:00 - 10:59)</option>
                        <option value="6" <?php if ($currentTimeIndex == 6) echo 'selected'; ?>>午時 (11:00 - 12:59)</option>
                        <option value="7" <?php if ($currentTimeIndex == 7) echo 'selected'; ?>>未時 (13:00 - 14:59)</option>
                        <option value="8" <?php if ($currentTimeIndex == 8) echo 'selected'; ?>>申時 (15:00 - 16:59)</option>
                        <option value="9" <?php if ($currentTimeIndex == 9) echo 'selected'; ?>>酉時 (17:00 - 18:59)</option>
                        <option value="10" <?php if ($currentTimeIndex == 10) echo 'selected'; ?>>戌時 (19:00 - 20:59)</option>
                        <option value="11" <?php if ($currentTimeIndex == 11) echo 'selected'; ?>>亥時 (21:00 - 22:59)</option>
                    </select>
                    
                    <label class="mr-2 mb-2" for="gender">【性別】</label>
                    <select id="gender" class="form-control mr-3 mb-2">
                        <option value="male">男</option>
                        <option value="female">女</option>
                    </select>
                    
                    <button type="button" id="calculateButton" class="btn btn-warning mb-2">開始排盤</button>
                    <button type="button" id="downloadImageButton" class="btn btn-success mb-2 ml-2">下載 PNG</button>
                
                </form>
            </div>
            
            <hr>
        
            <div id="chart-container">
                <div id="chart-grid">
                    <div id="chart-info" class="card"></div>
                </div>
            </div>
        
            <div id="ai-analysis-container" class="card mt-4">
                <div class="card-header h5 text-center">
                    <span role="img" aria-label="robot">🤖</span> AI 星盤解讀
                </div>
                <div class="card-body">
                    <p class="card-text text-center text-muted">點擊按鈕，產生專屬的 AI 提示詞，複製後即可找 AI 解讀。</p>
                    <button id="generateAiButton" class="btn btn-info btn-block">產生 AI 提示詞</button>
                    <div id="ai-loading" class="text-center text-muted p-3" style="display: none;">
                        正在產生提示詞，請稍候...
                    </div>
                    <div id="ai-result">
                        </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/iztro/dist/iztro.min.js"></script>

<script src="ziwei-app.js?v=1.3" defer></script> 

</body> 
</html>