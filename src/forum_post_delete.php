<?php

#
# Usage: In posting.php, following delete_post():
# 
# require_once('/home/uckelman/site-src/bridge/src/forum_post_delete.php'); 
#

try {
  remove_post($post_id);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

function remove_post($postId) {
  require_once(__DIR__ . '/Bridge.php');

  $bridge = new Bridge();
  $bridge->removePost($postId);
}

?>
