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
 
  $subject = $post_data['post_subject'];

  $time = $phpbb->getPostTime($postId);
  $date = date(DATE_RFC2822, $time);
  $messageId = build_message_id($time, $postId, $_SERVER['SERVER_NAME']);
 
  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                  dirname($_SERVER['SCRIPT_NAME']);

  $body = $data['message'];

/*
  print '<p>';
  var_dump($data);
  var_dump($post_data);
  var_dump($messageId);
  print '</p>';
*/

/*
  $bridge = new Bridge();

  $to = $bridge->getLists($forumId);

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
