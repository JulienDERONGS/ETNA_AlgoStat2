<?php
class             Sort
{
  private         $str;
  private         $regex_int_float_negornot;
  private         $sort_time_start;
  private         $sort_time;
  private         $sort_cost;
  private         $sort_total_nb;
  private         $sorted_array;
  private         $started;

  function __construct($str)
  {
    $this->regex_int_float_negornot = "/(-?\d+(\.|\,)?\d+)|(-?\d+)/";
    $this->str = htmlspecialchars($str);
    $sort_time_start = 0;
    $sort_time = 0;
    $sort_cost = 0;
    $sort_total_nb = 0;
    $sorted_array = array();
    $started = FALSE;
  }

  function __destruct()
  {
    unset($this->str);
    unset($this->regex_int_float_negornot);
    unset($this->sort_time_start);
    unset($this->sort_time);
    unset($this->sort_cost);
    unset($this->sort_total_nb);
    unset($this->sorted_array);
    unset($this->started);
  }

  function getSortTime()      {return ($this->sort_time);}
  function getSortCost()      {return ($this->sort_cost);}
  function getSortTotalNb()   {return ($this->sort_total_nb);}
  function getSortedArray()   {return ($this->sorted_array);}

  function get_clean_data()
  {
    $data = array();
    $clean_data = array();
    $clean_data_floats = array();

    // Clean raw data
    preg_match_all($this->regex_int_float_negornot, $this->str, $matches);

    // Add cleaned data
    if (empty($matches))
    {
      return (NULL);
    }
    foreach ($matches as $key => $value)
    {
      array_push($data, $value);
    }
    $clean_data = str_replace(",", ".", $data[0]);
    foreach ($clean_data as $key => $value)
    {
      array_push($clean_data_floats, floatval($value));
    }
    return ($clean_data_floats);
  }

  // Sorting functions pointer, should use form types as $type
  function sort_by_type($type, $seq)
  {
    return ($this->$type($seq));
  }

  protected function swap_array($array, $left, $right)
  {
  	$tmp = $array[$left];
    $array[$left] = $array[$right];
  	$array[$right] = $tmp;
  	return $array;
  }

  protected function insertion($seq)
  {
    $this->sort_time = microtime(true);
    $this->sort_cost = 0;

  	for ($i = 0; $i < count($seq); $i++)
    {
  		$value = $seq[$i];
  		$j = ($i - 1);
  		while ($j >= 0 && $seq[$j] > $value)
      {
  			$seq[$j + 1] = $seq[$j];
  			$j--;
        $this->sort_cost++;
  		}
  		$seq[$j + 1] = $value;
      $this->sort_cost += (count($seq));
  	}
    $this->sort_time = microtime(true) - $this->sort_time;
    $this->sorted_array = $seq;
    $this->sort_total_nb = count($seq);
    return ($this->getSortedArray());
  }

  protected function selection($seq)
  {
    $this->sort_time = microtime(true);
    $this->sort_cost = 0;

    for ($i = 0; $i < count($seq) - 1; $i++)
    {
    	$min = $i;
    	for ($j = $i + 1; $j < count($seq); $j++)
      {
    		if ($seq[$j] < $seq[$min])
        {
    			$min = $j;
    		}
        $this->sort_cost++;
    	}
      $seq = $this->swap_array($seq, $i, $min);
      $this->sort_cost++;
    }
    $this->sort_time = microtime(true) - $this->sort_time;
    $this->sorted_array = $seq;
    $this->sort_total_nb = count($seq);
    return ($this->getSortedArray());
  }

  protected function bubble($seq)
  {
    $this->sort_time = microtime(true);
    $this->sort_cost = 0;

    $swapped = true;
    while ($swapped)
  	{
  		$swapped = false;
  		for ($i = 0, $seq_max = count($seq) - 1; $i < $seq_max; $i++)
  		{
  			if ($seq[$i] > $seq[$i + 1])
  			{
  				$seq = $this->swap_array($seq, $i, $i + 1);
  				$swapped = true;
  			}
        $this->sort_cost++;
  		}
      $this->sort_cost++;
  	}
    $this->sort_time = microtime(true) - $this->sort_time;
    $this->sorted_array = $seq;
    $this->sort_total_nb = count($seq);
    return ($this->getSortedArray());
  }

  protected function shell($seq)
  {
    $this->sort_time = microtime(true);
    $this->sort_cost = 0;

  	$round = round(count($seq) / 2);
  	while ($round > 0)
  	{
  		for ($i = $round; $i < count($seq); $i++)
      {
  			$tmp = $seq[$i];
  			$j = $i;
  			while ($j >= $round && $seq[$j - $round] > $tmp)
  			{
  				$seq[$j] = $seq[$j - $round];
  				$j -= $round;
          $this->sort_cost++;
  			}
  			$seq[$j] = $tmp;
        $this->sort_cost++;
  		}
  		$round = round($round / 2.2);
      $this->sort_cost++;
  	}
    $this->sort_time = microtime(true) - $this->sort_time;
    $this->sorted_array = $seq;
    $this->sort_total_nb = count($seq);
    return ($this->getSortedArray());
  }

  protected function quick($seq)
  {
    // Initialize stats during first iteration
    if ($this->started == false)
    {
      $this->sort_time_start = microtime(true);
      $this->sort_cost = 0;
      $this->sort_total_nb = count($seq);
      $this->started = true;
    }

  	$loe = array();
    $gt = array();
  	if (count($seq) < 2) // Sub-sort finished
  	{
      return ($seq);
  	}
  	$pivot_key = key($seq);
    // Adding the cost of array_shift() to the total, O(n) for it has to reindex all keys
    $this->sort_cost += (count($seq));
  	$pivot = array_shift($seq);
  	foreach ($seq as $val)
  	{
  		if ($val <= $pivot)
  		{
  			$loe[] = $val;
  		}
      elseif ($val > $pivot)
  		{
  			$gt[] = $val;
  		}
      $this->sort_cost++;
  	}
    $this->sort_cost++;
    $this->sort_time = microtime(true) - $this->sort_time_start;
    $this->sorted_array = $seq;
  	return (array_merge($this->quick($loe), array($pivot_key => $pivot), $this->quick($gt)));
  }

  protected function comb($seq)
  {
    $this->sort_time = microtime(true);
    $this->sort_cost = 0;

  	$gap = count($seq);
    $swap = true;
  	while ($gap > 1 || $swap)
    {
  		if ($gap > 1)
      {
          $gap /= 1.25;
      }
   		$swap = false;
  		$i = 0;
  		while ($i + $gap < count($seq))
      {
  			if ($seq[$i] > $seq[$i + $gap])
        {
  				$this->swap_array($seq, $i, $i + $gap);
  				$swap = true;
  			}
  			$i++;
        $this->sort_cost++;
  		}
      $this->sort_cost++;
  	}
    $this->sort_time = microtime(true) - $this->sort_time;
    $this->sorted_array = $seq;
    $this->sort_total_nb = count($seq);
    return ($this->getSortedArray());
  }

  protected function merge($seq)
  {
    // Initialize stats during first iteration
    if ($this->started == false)
    {
      $this->sort_time_start = microtime(true);
      $this->sort_cost = 0;
      $this->sort_total_nb = count($seq);
      $this->started = true;
    }
  	if (count($seq) == 1)
    {
      return $seq;
    }
  	$mid = count($seq) / 2;
    // Adding the cost of the two array_slice() to the total, O(n) for they have to reindex all keys
    $this->sort_cost += (count($seq));
    $left = array_slice($seq, 0, $mid);
    $right = array_slice($seq, $mid);
  	$left = $this->merge($left);
  	$right = $this->merge($right);
  	return ($this->merge_sort_left_right($left, $right));
  }

  function merge_sort_left_right($left, $right)
  {
  	$seq = array();
  	while (count($left) > 0 && count($right) > 0)
    {
  		if ($left[0] > $right[0])
      {
  			$seq[] = $right[0];
  			$right = array_slice($right, 1);
  		}
      else
      {
  			$seq[] = $left[0];
  			$left = array_slice($left, 1);
  		}
      $this->sort_cost++;
  	}
  	while (count($left) > 0)
    {
  		$seq[] = $left[0];
  		$left = array_slice($left, 1);
      $this->sort_cost += (count($seq));
  	}
  	while (count($right) > 0)
    {
  		$seq[] = $right[0];
  		$right = array_slice($right, 1);
      $this->sort_cost += (count($seq));
  	}
    if (count($seq) == $this->sort_total_nb)
    {
      $this->sort_time = microtime(true) - $this->sort_time_start;
      $this->sorted_array = $seq;
      return ($this->getSortedArray());
    }
    else
    {
      return ($seq);
    }
  }
}
?>
