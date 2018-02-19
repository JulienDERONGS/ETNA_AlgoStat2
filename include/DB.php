<?php
/**
 * Database connexion and requests classes
 */
class             DB extends SingletonFactory
{
  private static  $isConnected = NULL;
  private static  $conn = NULL;

  function        connect()
  {
    if (!static::$isConnected)
    {
      try
      {
        $config = Config::getInstance();
        $conn = new PDO("mysql:host=". $config->getIP() .";dbname=". $config->getDBname(), $config->getUsername(), $config->getPassword());
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        static::$conn = $conn;
        static::$isConnected = TRUE;
        return ($conn);
      }
      catch (PDOException $e)
      {
        echo "Error: ". $e->getMessage();
        return (NULL);
      }
    }
    else
    {
      return (static::$conn);
    }
  }

  // Add stats of the last run sort into the DB
  function        add_data($sort, $sort_type_name)
  {
    try
    {
      $conn = $this->connect();
      $stmt = $conn->prepare("SELECT  `sort_type_id`
                              FROM    `Sort_type`
                              WHERE   `sort_type_name` = :sort_type_name
                              LIMIT   1
                              ");
      $stmt->bindParam(":sort_type_name", $sort_type_name, PDO::PARAM_STR);
      $stmt->execute();
      $sort_type_id = $stmt->fetch();

      // Insert the last sort's statistics into the database
      $stmt = $conn->prepare("INSERT INTO Stat (`FK_sort_type_id`, `stat_time`, `stat_cost`, `stat_total_nb`)
                              VALUES ($sort_type_id[0], :stat_time, :stat_cost, :stat_total_nb)"
                            );
      $stmt->bindValue(":stat_time", strval($sort->getSortTime()), PDO::PARAM_STR);
      $stmt->bindValue(":stat_cost", $sort->getSortCost(), PDO::PARAM_INT);
      $stmt->bindValue(":stat_total_nb", $sort->getSortTotalNb(), PDO::PARAM_STR);
      $stmt->execute();
    }
    catch(PDOException $e)
    {
      echo "Error: ". $e->getMessage();
      $conn = NULL;
      return (NULL);
    }
    $conn = NULL;
    return (TRUE);
  }

  /*** Adds a random sequence (between 0 and $t numbers) of random float
  **** numbers $t times in DB, using each algorithm, for better comparison purposes
  **** (150 times max, the server can't support more apparently)
  ***/
  function          fillRandomSequencesIntoDB($timesUsingEachSortType)
  {
    function        random_float ($min, $max)
    {
      return ($min + lcg_value() * (abs($max - $min)));
    }

    $str = "";
    $db = DB::getInstance();
    $db->connect();

    // Security checks
    $t = intval($timesUsingEachSortType, 10);
    unset($timesUsingEachSortType);
    if ($t > 150)
    {
      $t = 150;
    }
    elseif ($t < 1)
    {
      $t = 1;
    }

    for ($i = 0; $i < 7; $i++)
    {
      if ($i == 0) $sort_type = "insertion";
      if ($i == 1) $sort_type = "selection";
      if ($i == 2) $sort_type = "bubble";
      if ($i == 3) $sort_type = "shell";
      if ($i == 4) $sort_type = "quick";
      if ($i == 5) $sort_type = "comb";
      if ($i == 6) $sort_type = "merge";
      for ($j = 0; $j < $t; $j++)
      {
        $str = "";
        $nb = mt_rand(2, $t);
        for ($k = 0; $k < $nb; $k++)
        {
          $str .= strval(random_float(-500, 500)) . " ";
        }
        // Convert to float and sort the string
        $sort = new Sort($str);
        $sort->sort_by_type($sort_type, $sort->get_clean_data());

        // Add the sorted sequence into the DB
        $db->add_data($sort, $sort_type);

        // Unset $sort for the next use
        unset($sort);
      }
    }
  }

  protected function       averageTimeCostByNb($raw_sorted_stats)
  {
    if (!isset($raw_sorted_stats[0]))
    {
      return (NULL);
    }
    $i = 0;
    $j = 0;
    $nb = $raw_sorted_stats[0]['stat_total_nb'];
    $sum_times = 0;
    $sum_costs = 0;
    $averaged_stats = array(array());

    while (isset($raw_sorted_stats[0]))
    {
      if ($raw_sorted_stats[0]['stat_total_nb'] == $nb)
      {
        $sum_times += $raw_sorted_stats[0]['stat_time'];
        $sum_costs += $raw_sorted_stats[0]['stat_cost'];
        $i++;
        array_shift($raw_sorted_stats);
      }
      else
      {
        $averaged_stats[$j] = array(
                                "nb" => $nb,
                                "time" => ($sum_times / $i),
                                "cost" => ($sum_costs / $i));
        $nb = $raw_sorted_stats[0]['stat_total_nb'];
        $sum_times = 0;
        $sum_costs = 0;
        $i = 0;
        $j++;
      }
      //echo "nb = $nb\nsum_times = $sum_times\nsum_costs = $sum_costs\n";
    }
    return ($averaged_stats);
  }

  ***REMOVED*** function          getJsonAvgStatsBySortType($type)
  {
    $raw_sorted_stats = array(array());
    $conn = $this->connect();

    // Insert all stats in an array
    $stmt = $conn->prepare("SELECT    s.`stat_total_nb`, s.`stat_time`, s.`stat_cost`
                            FROM      `Stat` s, `Sort_type` st
                            WHERE     st.`sort_type_id` = s.`FK_sort_type_id`
                            AND       `sort_type_name` = :type
                            ORDER BY  s.`stat_total_nb` ASC
                            ;");
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    $raw_sorted_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return (json_encode($this->averageTimeCostByNb($raw_sorted_stats), JSON_PRETTY_PRINT));
  }
}
?>
