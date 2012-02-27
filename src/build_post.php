<?php

function build_tag_pattern($tag) {
  return '/' . preg_quote($tag, '/') . '\\s*/';
}

function build_post_subject($listtag, $forumtag, $subject) {
  // strip the '[list]' and '[forum]' tags
  $tagpat = '/(' . preg_quote($listtag, '/') .
             '|' . preg_quote($forumtag, '/') . ')\\s*/';
  $subject = preg_replace($tagpat, '', $subject);

  // strip leading sequences of Re-equivalents
  if (preg_match(
    '/^((RE|AW|SV|VS)(\\[\\d+\\])?:\\s*)+/i',
    $subject, $m, PREG_OFFSET_CAPTURE
  )) {
    $subject = substr($subject, $m[0][1]);
  }

  // ensure nonempty subject
  $subject = trim($subject);
  if ($subject == '') {
    $subject = '(no subject)';
  }

  return $subject; 
}

?>
