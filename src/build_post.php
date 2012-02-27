<?php

function build_post_subject($listtag, $forumtag, $subject) {
  // strip the '[list]' and '[forum]' tags
  $tagpat = '/(' . preg_quote($listtag, '/') .
             '|' . preg_quote($forumtag, '/') . ')\\s*/';
  $subj = preg_replace($tagpat, '', $subject);

  // strip leading sequences of Re-equivalents and Edit
  $re = '/^(?:(?:RE|AW|SV|VS|EDIT)(?:\\[\\d+\\])?:\\s*)+/i';
  if (preg_match($re, $subj, $m)) {
    $subj = substr($subj, strlen($m[0]));
  }

  // ensure nonempty subject
  $subj = trim($subj);
  if ($subj == '') {
    $subj = '(no subject)';
  }

  return $subj;
}

?>
