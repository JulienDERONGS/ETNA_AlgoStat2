<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <title>AlgoStat2 : Algorithms time and cost comparisons</title>
      <link href="styles/style.css" rel="stylesheet" type="text/css">
      <script src="include/js/echarts.js"></script>
      <?php
        // Initialize classes and session
        session_start();
        require_once "include/Autoloader.php";
        $autoloader = new Autoloader();
        $config = Config::getInstance();
        $db = DB::getInstance();
        $db->connect();
        /////////////// DEBUG
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ///////////////
        ?>
  </head>
  <div class="wrapper">
    <div class="header">
      <a href="index.php">SORT</a>
      <a href="graphs.php">GRAPHS</a>
    </div>
  </div>
  <body>
    <div class="push"></div>

    <!-- prepare a DOM container with width and height -->
  <div id="graphs" style="width: 800px;height:600px;"></div>
  <script type="text/javascript">
  var db_insertion = <?php echo $db->getTimesAndCostsBySortType("insertion"); ?>;
  var db_selection = <?php echo $db->getTimesAndCostsBySortType("selection"); ?>;
  var db_bubble = <?php echo $db->getTimesAndCostsBySortType("bubble"); ?>;
  var fact = []

  let i = 0;
  for(const one of db_insertion) {
    fact.push(i + 1);
    i++;
  }

  // based on prepared DOM, initialize echarts instance
  var myChart = echarts.init(document.getElementById('graphs'));

  option =
  {
    title: {
        text: 'Y : [Time * Cost] / X : [Each algorithm run]'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:[db_insertion, 'Selection', 'Bubble']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: fact
    },
    yAxis: {
        type: 'value'
    },
    series: [
    {
        data: db_insertion,
        type: 'line',
        smooth: true
    },
    {
        data: db_selection,
        type: 'line',
        smooth: true
    },
    {
        data: db_bubble,
        type: 'line',
        smooth: true
    }
    ]
};
        // use configuration item and data specified to show chart
        myChart.setOption(option);
  </script>
  </body>
</html>
