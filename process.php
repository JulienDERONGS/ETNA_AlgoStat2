<?php
  // Initialize session & classes
  if (isset($_SESSION))
  {
    session_unset();
    session_destroy();
  }
  session_start();
  $_SESSION['clean_seq'] = "";
  $_SESSION['sorted_seq'] = "";
  $_SESSION['error'] = "";
  require_once "include/Autoloader.php";
  $autoloader = new Autoloader();

  // Fill form processing
  if (isset($_POST['fill_submit']) && isset($_POST['fill_nb']))
    {
      $_SESSION['error'] = "Fill process started"; // Security
      $db = DB::getInstance();
      $db->connect();
      $db->fillRandomSequencesIntoDB(intval($_POST['fill_nb']));
      $_SESSION['error'] = "Fill successfully done !";
      header("Location: index.php");
    }

  // Sequence addition form processing
  // Incorrect form input -> redirection + error, else start sorting process
  if (isset($_POST["submit"]) && isset($_POST["type"]) &&
  isset($_POST["sequence"]))
  {
    if ($_POST['type'] == "insertion" || $_POST['type'] == "selection" ||
    $_POST['type'] == "bubble" || $_POST['type'] == "shell"  ||
    $_POST['type'] == "quick" || $_POST['type'] == "comb" ||
    $_POST['type'] == "merge")
    {
      try
      {
        // Sanitize user input & clean it for future sorting
        $sort = new Sort($_POST['sequence']);
        $clean_seq = $sort->get_clean_data();
      }
      catch (Exception $e)
      {
        if (empty($_SESSION['error']))
        {
          $_SESSION['error'] = $e->getMessage();
        }
        header("Location: index.php");
      }
      if (!isset($clean_seq) || empty($clean_seq)) // empty user sequence
      {
        if (empty($_SESSION['error']))
        {
          $_SESSION['error'] = "Please enter a correct sequence of numbers.";
          header("Location: index.php");
        }
      }
      else // start sorting
      {
        $_SESSION['clean_seq'] = $clean_seq;
        $_SESSION['sorted_seq'] = $sort->sort_by_type($_POST['type'], $clean_seq);
        $_SESSION['cost'] = $sort->getSortCost();
        $_SESSION['time'] = $sort->getSortTime();
        $_SESSION['nb'] = $sort->getSortTotalNb();
      }
      if (empty($_SESSION['sorted_seq'])) // empty sorted array
      {
        if (!$_SESSION['error'] || empty($_SESSION['error']))
        {
          $_SESSION['error'] = "Sequence is empty after sorting.";
        }
        header("Location: index.php");
      }
    }
    else // wrong sort type
    {
      if (!$_SESSION['error'] || empty($_SESSION['error']))
      {
        $_SESSION['error'] = "Wrong sort type.";
      }
      header("Location: index.php");
    }
  }
  else // empty form element
  {
    if (!$_SESSION['error'] || empty($_SESSION['error']))
    {
      $_SESSION['error'] = "Empty form element, please fill them all.";
    }
    header("Location: index.php");
  }

  // Let the DB class add results to the db
  $db = DB::getInstance();
  $db->connect();
  if (!$_SESSION['error'] || empty($_SESSION['error']))
  {
    $db->add_data($sort, $_POST['type']);
  }
  if (isset($_POST['submit']))    { unset($_POST['submit']); }
  if (isset($_POST['type']))      { unset($_POST['type']); }
  if (isset($_POST['sequence']))  { unset($_POST['sequence']); }
  header("Location: index.php");
?>
