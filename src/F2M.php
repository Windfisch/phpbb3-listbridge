<?php

try {
  send_to_lists($user, $data, $post_data);
}
catch (Exception $e) {
  print "<p>$e</p>\n";
}

function send_to_lists($user, $data, $post_data) {

  require_once(__DIR__ . '/PhpBB3.php');
  require_once(__DIR__ . '/Util.php');

  $phpbb = new PhpBB3();

/*
  require_once('Mail.php');

  require_once(__DIR__ . '/Bridge.php');
*/

  $postId = $data['post_id'];

  $userName = $user->data['username'];
  $userEmail = $user->data['user_email'];

  $from = utf8_quote($userName) . ' <' . $userEmail . '>';
  $sender = 'forum@test.nomic.net';
  $subject = utf8_quote($post_data['post_subject']);

  $time = $phpbb->getPostTime($postId);
  if ($time === false) {
    throw new Exception('no post time: ' . $postId);
  }

  $date = date(DATE_RFC2822, $time);
  $messageId = build_message_id($time, $postId, $_SERVER['SERVER_NAME']);
 
  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                  dirname($_SERVER['SCRIPT_NAME']);

  $body = $data['message'];

  print '<p>';
  var_dump($data);
  var_dump($post_data);
  print '</p>';

/*
  $bridge = new Bridge();

  $to = $bridge->getLists($forumId);
  if ($to == false) {

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

?>
