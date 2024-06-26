<?php include 'metaseo.php'; ?>
<meta name="description" content="探索不同年份的春牛芒神服色及其象徵意義，了解每一年圖中描繪的豐收預兆和文化內涵，感受中國傳統藝術的魅力。">
  <title><?php
if (!empty($_GET['year'])) {
    echo htmlspecialchars($_GET['year']) . "年";
}
?>春牛芒神服色</title>
<?php include 'head.php'; ?>
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
<div class="container"><div class="row"><div class="col-md-12">
	<form class="form-inline ml-auto" id="year-form" method="get">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">選擇年份</span>
        </div>
        <input type="number" id="year" name="year" value="<?php echo isset($_GET['year']) ? htmlspecialchars($_GET['year']) : date('Y'); ?>" class="form-control" style="width: 90px;">
        <div class="input-group-append">
            <button type="submit" class="btn btn-warning">進呈</button>
        </div>
    </div>
</form>
<hr/>
		  <h2 class="mb-3">【<?php
if (!empty($_GET['year'])) {
    echo htmlspecialchars($_GET['year']) . "年";
}
?>春牛芒神服色】</h2>
<?php
	
require 'Lunar.php';

use com\nlf\calendar\Lunar;
use com\nlf\calendar\Solar;

	
if (isset($_GET['year'])) {

         // 獲取使用者輸入的年份
        $year = (int)$_GET['year'];

    // 初始化為農曆正月初一
    $lunar = Lunar::fromYmd($year, 1, 1);
	$solar = $lunar->getSolar();
		 
include_once("springoxcon.php");
echo '【'.$oxcontent.'】';  
}
?>
</div></div></div>
<?php include 'footer.php'; ?>	
</body>	
</html>
