<?php

function build_post_subject($listtag, $forumtag, $subject) {
  // strip the '[list]' and '[forum]' tags
  $tagpat = '/(' . preg_quote($listtag, '/') .
             '|' . preg_quote($forumtag, '/') . ')\\s*/';
  $subj = preg_replace($tagpat, '', $subject);

  // strip leading sequences of Re-equivalents
  if (preg_match('/^(?:(?:RE|AW|SV|VS)(?:\\[\\d+\\])?:\\s*)+/i', $subj, $m)) {
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
