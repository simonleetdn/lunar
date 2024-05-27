<?php include 'metaseo.php'; ?>
  <title>農民曆</title>
<?php include 'head.php'; ?>

<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card text-white bg-danger">
          <div class="card-body text-center">
            <h5 class="card-title">歡迎使用農民曆（黃曆、通勝）查詢工具</h5>
            <p class="card-text">點擊下方按鈕查看農民曆</p>
			<?php
			$year = date("Y");
    		$month = date("m");
			echo '<a href=lunardate.php?year-month='.$year.'-'.$month.' class="btn btn-warning mb-4">';
			echo $year.'年'.$month.'月農民曆</a></br/>';
			echo '<a href=yidate.php class="btn btn-warning mb-4">每年宜日速查</a><br/>';
			echo '<a href=yuangang.php class="btn btn-warning">袁天罡稱骨算命</a><br/>';
			//echo '<a href=springox.php?year='.$year.' class="btn btn-warning mt-4">'.$year.'年春牛芒神服色</a>';  
			?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
