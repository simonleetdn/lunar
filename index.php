<?php include 'metaseo.php'; ?>
<title>農民曆</title>
<?php include 'head.php'; ?>

<style>
body {
  background: url('square.svg') no-repeat center center fixed;
  background-size: cover;
  color: #333;
}

.card {
  background: rgba(255, 255, 255, 0.9);
  border: none;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-warning {
  font-weight: bold;
}


.card-title {
  font-size: 1.75rem;
  font-weight: bold;
}

.card-text {
  font-size: 1.25rem;
}

.container {
  margin-top: 10%;
}
</style>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">歡迎使用農民曆（黃曆、通勝）查詢工具</h5>
            <p class="card-text">點擊下方按鈕查看農民曆</p>
            <?php
            $year = date("Y");
            $month = date("m");
            echo '<a href="lunardate.php?year-month='.$year.'-'.$month.'" class="btn btn-warning mb-4">';
            echo $year.'年'.$month.'月農民曆</a><br/>';
            echo '<a href="yidate.php" class="btn btn-warning mb-4">每年宜日速查</a><br/>';
			echo '<a href="/calendar/" class="btn btn-warning mb-4">每日自動更新的日曆</a><br/>';
            echo '<a href="yuangang.php" class="btn btn-warning">袁天罡稱骨算命</a><br/>';
            echo '<a href="ziwei.php" class="btn btn-warning">紫微斗數線上排盤</a><br/>';
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
