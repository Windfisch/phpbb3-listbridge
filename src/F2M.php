<?php

try {
  send_to_lists($user, $data, $post_data);
}
catch (Exception $e) {
  print "<p>$e</p>\n";
}

function send_to_lists($user, $data, $post_data) {

  require_once(__DIR__ . '/PhpBB3.php');

  $phpbb = new PhpBB3();

/*
  require_once('Mail.php');

  require_once(__DIR__ . '/Bridge.php');
  require_once(__DIR__ . '/Util.php');
*/

  $postId = $data['post_id'];

  $userName = $user->data['username'];
  $userEmail = $user->data['user_email'];
 
  $subject = $post_data['post_subject'];

  $time = $phpbb->getPostTime($postId);
  $date = date(DATE_RFC2822, $time);
  $messageId = mbuild_message_id($time, $postId, $_SERVER['SERVER_NAME']);
 
  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
              substr($_SERVER['SCRIPT_NAME'], 0, -strlen('/posting.php'));

  $body = $data['message'];

  print '<p>';
  var_dump($data);
  var_dump($post_data);
  var_dump($messageId);
  var_dump($forumURL);
  print '</p>';

/*
  $bridge = new Bridge();

  $to = $bridge->getLists($forumId);
  $messageId = build_message_id($postId);

  $headers = array(
    'To'           => implode(', ', $to),
    'From'         => "$userName <$userEmail>",
    'Subject'      => $subject,
    'Date',        => $date,
    'Message-Id',  => $messageId,
    'X-BeenThere'  => $forumURL,
  );






  $mailer = Mail::factory('sendmail');
  $mailer->send($to, $headers, $body);
*/

}

function get_post_time($postId) {
  throw_if_null($postId);

  global $db;

  $sql = 'SELECT post_time FROM ' . POSTS_TABLE . ' ' .
         'WHERE post_id = ' . $postId;

  $row = $this->get_exactly_one_row($sql);
  return $row ? $row['user_id'] : false;
}

?>
