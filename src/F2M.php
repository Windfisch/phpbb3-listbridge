<?php

try {
  send_to_lists($user, $data, $post_data);
}
catch (Exception $e) {
  print "<p>$e</p>\n";
}

function send_to_lists($user, $data, $post_data) {

  require_once('Mail.php');

  require_once(__DIR__ . '/Bridge.php');
  require_once(__DIR__ . '/PhpBB3.php');
  require_once(__DIR__ . '/Util.php');

  $postId = $data['post_id'];
  $forumId = $data['forum_id'];
  
  $bridge = new Bridge();

  $to = $bridge->getLists($forumId);
  if (count($to) == 0) {
    # No lists to send to, bail out.
    return;    
  }
  $to = implode(', ', $to);

  $userName = $user->data['username'];
  $userEmail = $user->data['user_email'];

  $from = utf8_quote($userName) . ' <' . $userEmail . '>';
  $sender = 'forum@test.nomic.net';
  $subject = utf8_quote($post_data['post_subject']);

  $phpbb = new PhpBB3();

  $time = $phpbb->getPostTime($postId);
  if ($time === false) {
    throw new Exception('no post time: ' . $postId);
  }

  $date = date(DATE_RFC2822, $time);
  $messageId = build_message_id($time, $postId, $_SERVER['SERVER_NAME']);
 
  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                  dirname($_SERVER['SCRIPT_NAME']);

  $body = $data['message'];

/*
  print '<p>';
  var_dump($data);
  var_dump($post_data);
  print '</p>';
*/

  # assemble the message headers
  $headers = array(
    'To'          => $to,
    'From'        => $from,
    'Subject'     => $subject,
    'Date'        => $date,
    'Message-Id'  => $messageId,
    'X-BeenThere' => $forumURL,
  );

  # send the message
  $mailer = Mail::factory('sendmail');
  $err = $mailer->send($to, $headers, $body);
  if (PEAR::isError($err)) {
    throw new Exception('Mail::send error: ' . $err->toString());
  }
}

?>