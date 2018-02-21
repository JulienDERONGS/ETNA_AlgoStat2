<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>AlgoStat2 : Algorithms' time and cost comparisons</title>
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

    <!-- Readme -->
    <div class=readme>
      The graphs are showing data of previously done sorts, which data is stored in the database.
    </br>Each sort type in the graphs can be activated/deactivated by clicking on their names,
      </br>at the top of each graph, for a better readability.
      </br>Graphs can be downloaded as images, by clicking on the arrow at the top-right corner.</br>
      </br>X axis : How many numbers were used in the sorted sequence.
      </br>Y axis [Time graphs] : How much time (in seconds) did the sorting take.
      </br>Y axis [Cost graphs] : How many iterations through the sequence did the sorting take.</br></br></br>
    </div>

    <!-- prepare DOM containers for the graphs to be displayed in -->
    <div id="graph_all_times" class="side" style="width: 1200px; height: 1200px; padding-top: 2em;"></div>
    <div id="graph_all_costs" class="side" style="width: 1200px; height: 1200px; padding-top: 2em;"></div>
    <div class="graphs">
      <div id="graph_insertion_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_insertion_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_selection_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_selection_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_bubble_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_bubble_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_shell_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_shell_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_quick_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_quick_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_comb_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_comb_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>
    <div class="graphs">
      <div id="graph_merge_time" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
      <div id="graph_merge_cost" class="side" style="width: 800px; height: 700px; padding-top: 4em;"></div>
    </div>

    <!-- JavaScript -->
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

      // Getting each stat for each sort from the DB into JS arrays
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

      // All times
      var chart_all_times = echarts.init(document.getElementById('graph_all_times'));
      var options_all_times =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
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
      // Use configuration options and data specified to render chart
      chart_all_times.setOption(options_all_times);

      // All costs
      var chart_all_costs = echarts.init(document.getElementById('graph_all_costs'));
      var options_all_costs =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
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
            data: data_insertion_cost,
          },
          {
            name: "Selection sort",
            type: "line",
            smooth: true,
            data: data_selection_cost,
          },
          {
            name: "Bubble sort",
            type: "line",
            smooth: true,
            data: data_bubble_cost,
          },
          {
            name: "Shell sort",
            type: "line",
            smooth: true,
            data: data_shell_cost,
          },
          {
            name: "Quick sort",
            type: "line",
            smooth: true,
            data: data_quick_cost,
          },
          {
            name: "Comb sort",
            type: "line",
            smooth: true,
            data: data_comb_cost,
          },
          {
            name: "Merge sort",
            type: "line",
            smooth: true,
            data: data_merge_cost,
          },
        ],
      };
      // Use configuration options and data specified to render chart
      chart_all_costs.setOption(options_all_costs);

      // Insertion sort : time
      var chart_insertion_time = echarts.init(document.getElementById('graph_insertion_time'));
      var options_insertion_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Insertion sort time']
        },
        series: [
          {
            name: "Insertion sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_insertion_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_insertion_time.setOption(options_insertion_time);

      // Insertion sort : cost
      var chart_insertion_cost = echarts.init(document.getElementById('graph_insertion_cost'));
      var options_insertion_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Insertion sort cost']
        },
        series: [
          {
            name: "Insertion sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_insertion_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_insertion_cost.setOption(options_insertion_cost);

      // Selection sort : time
      var chart_selection_time = echarts.init(document.getElementById('graph_selection_time'));
      var options_selection_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Selection sort time']
        },
        series: [
          {
            name: "Selection sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_selection_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_selection_time.setOption(options_selection_time);

      // Selection sort : cost
      var chart_selection_cost = echarts.init(document.getElementById('graph_selection_cost'));
      var options_selection_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Selection sort cost']
        },
        series: [
          {
            name: "Selection sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_selection_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_selection_cost.setOption(options_selection_cost);

      // Bubble sort : time
      var chart_bubble_time = echarts.init(document.getElementById('graph_bubble_time'));
      var options_bubble_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Bubble sort time']
        },
        series: [
          {
            name: "Bubble sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_bubble_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_bubble_time.setOption(options_bubble_time);

      // Bubble sort : cost
      var chart_bubble_cost = echarts.init(document.getElementById('graph_bubble_cost'));
      var options_bubble_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Bubble sort cost']
        },
        series: [
          {
            name: "Bubble sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_bubble_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_bubble_cost.setOption(options_bubble_cost);

      // Shell sort : time
      var chart_shell_time = echarts.init(document.getElementById('graph_shell_time'));
      var options_shell_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Shell sort time']
        },
        series: [
          {
            name: "Shell sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_shell_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_shell_time.setOption(options_shell_time);

      // Shell sort : cost
      var chart_shell_cost = echarts.init(document.getElementById('graph_shell_cost'));
      var options_shell_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Shell sort cost']
        },
        series: [
          {
            name: "Shell sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_shell_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_shell_cost.setOption(options_shell_cost);

      // Quick sort : time
      var chart_quick_time = echarts.init(document.getElementById('graph_quick_time'));
      var options_quick_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Quick sort time']
        },
        series: [
          {
            name: "Quick sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_quick_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_quick_time.setOption(options_quick_time);

      // Quick sort : cost
      var chart_quick_cost = echarts.init(document.getElementById('graph_quick_cost'));
      var options_quick_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Quick sort cost']
        },
        series: [
          {
            name: "Quick sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_quick_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_quick_cost.setOption(options_quick_cost);

      // Comb sort : time
      var chart_comb_time = echarts.init(document.getElementById('graph_comb_time'));
      var options_comb_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Comb sort time']
        },
        series: [
          {
            name: "Comb sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_comb_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_comb_time.setOption(options_comb_time);

      // Comb sort : cost
      var chart_comb_cost = echarts.init(document.getElementById('graph_comb_cost'));
      var options_comb_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Comb sort cost']
        },
        series: [
          {
            name: "Comb sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_comb_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_comb_cost.setOption(options_comb_cost);

      // Merge sort : time
      var chart_merge_time = echarts.init(document.getElementById('graph_merge_time'));
      var options_merge_time =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort time (sec)",
          type: 'value'
        }],
        legend: {
          data:['Merge sort time']
        },
        series: [
          {
            name: "Merge sort time",
            type: "line",
            smooth: true,
            color: "#2d70ad",
            data: data_merge_time
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_merge_time.setOption(options_merge_time);

      // Merge sort : cost
      var chart_merge_cost = echarts.init(document.getElementById('graph_merge_cost'));
      var options_merge_cost =
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
          name: "Nbs sorted",
          type: 'value'
        }],
        yAxis: [{
          name: "Sort cost",
          type: 'value'
        }],
        legend: {
          data:['Merge sort cost']
        },
        series: [
          {
            name: "Merge sort cost",
            type: "line",
            smooth: true,
            cost: "#801212",
            data: data_merge_cost
          }
        ],
      };
      // Use configuration options and data specified to render the graph
      chart_merge_cost.setOption(options_merge_cost);
  </script></br></br></br>
  </body>
  <footer>
      <h2>Sarah Al Janabi, Gabriel Cabanes, Julien Derongs, 2018</h2>
  </footer>
</html>
