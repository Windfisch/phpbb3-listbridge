<?php

try {
  remove_post($post_id);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

# TODO: call this from handle_post_delete in posting.php
function remove_post($postId) {
  require_once(__DIR__ . '/Bridge.php');

  $bridge = new Bridge();
  $bridge->removePost($postId);
}

?>
