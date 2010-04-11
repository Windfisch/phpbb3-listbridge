<?php

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

function build_message_id($time, $postId, $forumHost) {
  return "<$time.$postId.bridge@$forumHost>";
}

?>
