<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>AlgoStat2 : Algorithms time and cost comparisons</title>
    <link href="https://fonts.googleapis.com/css?family=EB+Garamond|Open+Sans|Roboto+Slab|Ubuntu+Condensed" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="styles/style.css" rel="stylesheet" type="text/css">
    <?php
    // IMPORTANT TODO : Clean all debugs
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
<div class="algo_form">
        <div class="heading">
            <a href="index.php">SORT</a>
            <a href="graphs.php">GRAPHS</a>
        </div>
    <form action="<?php echo $config->getProjPath() ?>/process.php" method="post">
        Sort type :</br>
        <select name="type">
            <option value="insertion">Insertion</option>
            <option value="selection">Selection</option>
            <option value="bubble">Bubble</option>
            <option value="shell">Shell</option>
            <option value="quick">Quick</option>
            <option value="comb">Comb</option>
            <option value="merge">Merge</option>
        </select></br></br>
        Sequence of numbers to sort :</br>
        <textarea name="sequence" rows="10" cols="80" placeholder="Any character can be written here, only integers and floats will be treated.&#13;&#10;Sequence exemple : cjebc33.3e4 r'8 ,,3,2;;-9.0-1"></textarea></br></br>
        <input type="submit" name="submit" value="Sort">
    </form>
</div>
</br></br></br>
<form class="randomFill_form" action="<?php echo $config->getProjPath() ?>/process.php" method="post">
  <textarea name="fill_nb" rows="5" cols="300" placeholder="This form will add random sequence of X numbers X times in DB using each algorithm, for better comparison purposes (X is for you to choose here, 150 max. /!\ Don't use excessively please /!\)."></textarea></br>
  <input type="submit" name="fill_submit" value="Click here after having entered a number between 1 and 150">
</form>
    <?php
    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) // Display error if there was one during sequence processing
    {
      echo ("<div class='sort_error'>");
      echo ($_SESSION['error']);
      echo ("</div>");
    }
    // Else display statistics
    else if (isset($_SESSION['clean_seq']) && isset($_SESSION['sorted_seq']))
    {
      echo ("<div class='last_sort'>");
      echo ("</br>Unsorted sequence : ");
      for ($i = 0; $i < (count($_SESSION['clean_seq'])); $i++)
      {
        echo ($_SESSION['clean_seq'][$i] ." ");
      }
      echo ("</br>Sorted sequence : ");
      for ($i = 0; $i < (count($_SESSION['clean_seq'])); $i++)
      {
        echo ($_SESSION['sorted_seq'][$i] ." ");
      }
      echo ("</br>This sequence contained ".$_SESSION['nb']." numbers,
      had a cost of ".$_SESSION['cost']." and executed itself in ".$_SESSION['time']." seconds.");

      echo ("</br>Cost by number in sequence : ".$_SESSION['cost']/$_SESSION['nb'].".");
      echo ("</br>Time by number in sequence : ".$_SESSION['time']/$_SESSION['nb'] . " seconds.");
    }
    session_unset();
    session_destroy();
    ?>

<footer>
    <h2>Gabriel Cabanes, Julien Derongs, Sarah Al Janabi, 2018</h2>
</footer>
  </body>
</html>
