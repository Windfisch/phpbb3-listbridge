<?php

function read_raw_message($url) {
  $input = file_get_contents($url);
  if (!$input) {
    throw new Exception("No input in $url");
  }
  return $input;
}

?>
