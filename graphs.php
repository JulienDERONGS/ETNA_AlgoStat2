<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>AlgoStat2 : Algorithms time and cost comparisons</title>
    <link href="styles/style.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=EB+Garamond|Open+Sans|Roboto+Slab|Ubuntu+Condensed" rel="stylesheet">
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

  <body>
    <!-- Header -->
    <section><h1>AlgoStat2 : Algorithms time and cost comparisons</h1></section>
    <div class="graphs_header">
      <div class="heading">
        <a href="index.php">SORT</a>
        <a href="graphs.php">GRAPHS</a>
      </div>
    </div>
    <div class="push"></div>

    <!-- prepare a DOM container with width and height -->
    <div id="graph_time" style="width: 800px; height: 600px; padding-top: 4em;"></div>
    <script type="text/javascript">

      function getTimeGraphFromJson(json)
      {
        const data = [];
        const each = Object.getOwnPropertyNames(json);
        for (const one of each)
        {
          data.push([json[one]["nb"], json[one]["time"]]);
        }
        return (data);
      }

      function getCostGraphFromJson(json)
      {
        const data = [];
        const each = Object.getOwnPropertyNames(json);
        for (const one of each)
        {
          data.push([json[one]["nb"], json[one]["cost"]]);
        }
        return (data);
      }

      var db_insertion = <?php echo $db->getJsonAvgStatsBySortType("insertion"); ?>;
      var data_insertion_time = getTimeGraphFromJson(db_insertion);
      var data_insertion_cost = getCostGraphFromJson(db_insertion);

      var db_selection = <?php echo $db->getJsonAvgStatsBySortType("selection"); ?>;
      var data_selection_time = getTimeGraphFromJson(db_selection);
      var data_selection_cost = getCostGraphFromJson(db_selection);

      var db_bubble = <?php echo $db->getJsonAvgStatsBySortType("bubble"); ?>;
      var data_bubble_time = getTimeGraphFromJson(db_bubble);
      var data_bubble_cost = getCostGraphFromJson(db_bubble);

      var db_shell = <?php echo $db->getJsonAvgStatsBySortType("shell"); ?>;
      var data_shell_time = getTimeGraphFromJson(db_shell);
      var data_shell_cost = getCostGraphFromJson(db_shell);

      var db_quick = <?php echo $db->getJsonAvgStatsBySortType("quick"); ?>;
      var data_quick_time = getTimeGraphFromJson(db_quick);
      var data_quick_cost = getCostGraphFromJson(db_quick);

      var db_comb = <?php echo $db->getJsonAvgStatsBySortType("comb"); ?>;
      var data_comb_time = getTimeGraphFromJson(db_comb);
      var data_comb_cost = getCostGraphFromJson(db_comb);

      var db_merge = <?php echo $db->getJsonAvgStatsBySortType("merge"); ?>;
      var data_merge_time = getTimeGraphFromJson(db_merge);
      var data_merge_cost = getCostGraphFromJson(db_merge);

      // Prepare a DOM element that will display the times chart
      var chart = echarts.init(document.getElementById('graph_time'));
      var options =
      {
        tooltip: {
            trigger: 'axis'
        },
        toolbox: {
          feature: {
            saveAsImage: {}
          }
        },
        xAxis: [{
          name: "Numbers in sequence",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time",
          type: 'value'
        }],
        legend: {
          data:['Insertion sort','Selection sort', 'Bubble sort', 'Shell sort',
                'Quick sort', 'Comb sort', 'Merge sort']
        },
        series: [
          {
            name: "Insertion sort",
            type: "line",
            smooth: true,
            data: data_insertion_time,
          },
          {
            name: "Selection sort",
            type: "line",
            smooth: true,
            data: data_selection_time,
          },
          {
            name: "Bubble sort",
            type: "line",
            smooth: true,
            data: data_bubble_time,
          },
          {
            name: "Shell sort",
            type: "line",
            smooth: true,
            data: data_shell_time,
          },
          {
            name: "Quick sort",
            type: "line",
            smooth: true,
            data: data_quick_time,
          },
          {
            name: "Comb sort",
            type: "line",
            smooth: true,
            data: data_comb_time,
          },
          {
            name: "Merge sort",
            type: "line",
            smooth: true,
            data: data_merge_time,
          },
        ],
      };

      // Use configuration options and data specified to show chart
      chart.setOption(options);
  </script>
  </body>
</html>
