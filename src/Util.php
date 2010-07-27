<?php

function throw_if_null($arg) {
  if ($arg === null) throw new Exception('argument is null');
}

function build_message_id($postId, $editId, $time, $forumHost) {
  return "<$time.$postId.$editId.bridge@$forumHost>";
}

function is_ascii($string) {
  return !preg_match('/[^[:ascii:]]/', $str);
}

function utf8_quote($string) {
  return '=?UTF-8?B?' . base64_encode($string) . '?=';
}

?>
