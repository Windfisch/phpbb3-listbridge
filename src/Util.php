<?php

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

?>
