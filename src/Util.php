<?php

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

function build_message_id($postId, $forumHost) {
  return '<' . time() . ".bridge.$postId@$forumHost>";
}

?>
