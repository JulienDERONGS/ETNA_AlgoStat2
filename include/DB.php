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

  function          getTimesBySortType($type)
  {
    $conn = $this->connect();
    $stmt = $conn->prepare("SELECT  s.`stat_time`
                            FROM    `Stat` s, `Sort_type` st
                            WHERE   st.`sort_type_id` = s.`FK_sort_type_id`
                            AND     `sort_type_name` = :type
                            ");
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    $time = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $conn = NULL;
    return (json_encode($time));
  }

  function          getCostsBySortType($type)
  {
    $conn = $this->connect();
    $stmt = $conn->prepare("SELECT  s.`stat_cost`
                            FROM    `Stat` s, `Sort_type` st
                            WHERE   st.`sort_type_id` = s.`FK_sort_type_id`
                            AND     `sort_type_name` = :type
                            ");
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    $cost = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $conn = NULL;
    return (json_encode($cost));
  }

  function          getTimesAndCostsBySortType($type)
  {
    $conn = $this->connect();
    $stmt = $conn->prepare("SELECT  s.`stat_time`
                            FROM    `Stat` s, `Sort_type` st
                            WHERE   st.`sort_type_id` = s.`FK_sort_type_id`
                            AND     `sort_type_name` = :type
                            ");
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    $time = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("SELECT  s.`stat_cost`
                            FROM    `Stat` s, `Sort_type` st
                            WHERE   st.`sort_type_id` = s.`FK_sort_type_id`
                            AND     `sort_type_name` = :type
                            ");
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);
    $stmt->execute();
    $cost = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $conn = NULL;
    $i = 0;
    while (isset($time[$i]) && isset($cost[$i]))
    {
      $tc[$i] = $time[$i] * $cost[$i];
      $i++;
    }
    return (json_encode($tc));
  }

  // Using Mersenne Twister algorithm (100 times max per algorithm)
  function          fillRandomSequencesIntoDB($timesUsingEachSortType)
  {
    $str = "";
    $db = DB::getInstance();
    $db->connect();

    // Security checks
    $t = intval($timesUsingEachSortType, 10);
    unset($timesUsingEachSortType);
    if ($t > 100 || $t < 1)
    {
      $t = 100;
    }

    // Adds random sequence of $t numbers $t times in DB using each algorithm, for better comparison purposes
    for ($i = 0; $i < 3; $i++)
    {
      if ($i == 0) $sort_type = "insertion";
      if ($i == 1) $sort_type = "selection";
      if ($i == 2) $sort_type = "bubble";
      for ($j = 0; $j < $t; $j++)
      {
        for ($k = 0; $k < $t; $k++)
        {
          $str .= strval(mt_rand(-42000, 42000)) . "";
        }
        // Convert to float and sort the string
        $sort = new Sort($str);
        $sort->sort_by_type($sort_type, $sort->get_clean_data());

        // Add the sorted sequence into the DB
        $db->add_data($sort, $sort_type);

        // Empty $str and unset $sort for the next use
        $str = "";
        unset($sort);
      }
    }
  }
}
?>
