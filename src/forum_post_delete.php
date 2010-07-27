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
  require_once('Log.php');
  $logger = &Log::singleton('file', '/var/log/listbridge', 'one');

  require_once(__DIR__ . '/Bridge.php');

  $bridge = new Bridge();
  if ($bridge->removePost($postId)) {
    $logger->info($postId . ' deleted');
  }
  else {
    $logger->info($postId . ' not found, not deleted');
  }
}

?>
