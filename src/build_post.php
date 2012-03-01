<?php

function build_post_subject($listtag, $forumtag, $subject) {
  // strip the '[list]' and '[forum]' tags
  $tagpat = '/(' . preg_quote($listtag, '/') .
             '|' . preg_quote($forumtag, '/') . ')\\s*/';
  $subject = preg_replace($tagpat, '', $subject);

  // strip leading sequences of Re-equivalents and Edit
  $re = '/^(?:(?:RE|AW|SV|VS|EDIT)(?:\\[\\d+\\])?:\\s*)+/i';
  if (preg_match($re, $subject, $m)) {
    $subject = substr($subject, strlen($m[0]));
  }

  // ensure nonempty subject
  $subject = trim($subject);
  if ($subject == '') {
    $subject = '(no subject)';
  }

  return $subject;
}

?>
