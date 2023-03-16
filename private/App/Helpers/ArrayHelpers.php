<?php

class ArrayHelpers
{
  public static function indexOf($stmt, $array)
  {
    foreach ($array as $key => $obj) {
      if ($stmt($obj, $key)) return $key;
    }
    return -1;
  }
}
