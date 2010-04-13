<?php

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

function build_message_id($time, $postId, $forumHost) {
  return "<$time.$postId.bridge@$forumHost>";
}

/*
function is_ascii($string) {
  return !preg_match('/[^\x00-\x7F]/S', $str);
}
*/

function utf8_quote($string) {
  return '=?UTF-8?B?' . base64_encode($string) . '?=';
}

?>
