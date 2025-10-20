<?php include 'metaseo.php'; ?>

<meta name="description" content="免費的紫微斗數排盤工具，提供12宮位星曜詳情、PNG下載和AI提示詞。">
<link rel="canonical" href="https://lunar.ioi.tw/ziwei.php" />
<title>紫微斗數線上排盤</title>

<?php include 'head.php'; ?>

<link rel="stylesheet" href="ziwei-style.css">

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
            <a id="yuangang" class="nav-link" href="yuangang.php?birthDateTime=<?php echo date('Y-m-d\TH:i'); ?>">稱骨算命</a>
          </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>紫微斗數線上排盤</h2>
            <p><form class="form-inline" style="justify-content: center;">
  
    <label class="mr-2 mb-2" for="name">【姓名】</label>
    <input type="text" id="name" class="form-control mr-3 mb-2" placeholder="請輸入姓名" style="width: 150px;">
    
    <label class="mr-2 mb-2" for="birthDate">【陽曆生日】</label>
    <input type="date" id="birthDate" class="form-control mr-3 mb-2" value="1990-01-01">
    
    <label class="mr-2 mb-2" for="birthTime">【出生時辰】</label>
    <select id="birthTime" class="form-control mr-3 mb-2">
        <option value="0">子時 (23:00 - 00:59)</option>
        <option value="1">丑時 (01:00 - 02:59)</option>
        <option value="2">寅時 (03:00 - 04:59)</option>
        <option value="3">卯時 (05:00 - 06:59)</option>
        <option value="4">辰時 (07:00 - 08:59)</option>
        <option value="5">巳時 (09:00 - 10:59)</option>
        <option value="6">午時 (11:00 - 12:59)</option>
        <option value="7">未時 (13:00 - 14:59)</option>
        <option value="8">申時 (15:00 - 16:59)</option>
        <option value="9">酉時 (17:00 - 18:59)</option>
        <option value="10">戌時 (19:00 - 20:59)</option>
        <option value="11">亥時 (21:00 - 22:59)</option>
    </select>
    
    <label class="mr-2 mb-2" for="gender">【性別】</label>
    <select id="gender" class="form-control mr-3 mb-2">
        <option value="male">男</option>
        <option value="female">女</option>
    </select>
    
    <button type="button" id="calculateButton" class="btn btn-warning mr-2 mb-2">開始排盤</button>
    <button type="button" id="downloadImageButton" class="btn mb-2">下載命盤</button>

</form></p>
        
            <div id="chart-container">
                <div id="chart-grid">
                    <div id="chart-info"></div>
                </div>
            </div>
        
            <div id="ai-analysis-container">
                <h2><span role="img" aria-label="robot">🤖</span> AI 星盤解讀</h2>
                <p>點擊按鈕，產生專屬的 AI 提示詞，複製後即可找 AI 解讀。</p>
                <button id="generateAiButton">產生 AI 提示詞</button>
                <div id="ai-loading" style="display: none;">
                    正在產生提示詞，請稍候...
                </div>
                <div id="ai-result">
                </div>
            </div>

            </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/iztro/dist/iztro.min.js"></script>

<script src="ziwei-app.js" defer></script> 

</body> 
</html>