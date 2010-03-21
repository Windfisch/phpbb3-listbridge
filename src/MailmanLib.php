<?php

function read_raw_message($url) {
  $input = file_get_contents($url);
  if (!$input) {
    trigger_error("No input in $url", E_USER_ERROR);
  }
  return $input;
}

?>
