<?php

try {
  send_to_lists($user, $data, $post_data);
}
catch (Exception $e) {
  print "<p>$e</p>\n";
}

function send_to_lists($user, $data, $post_data) {

/*
  require_once('Mail.php');

  require_once(__DIR__ . '/Bridge.php');
  require_once(__DIR__ . '/Util.php');
*/

  $postId = $data['post_id'];

  $userName = $user->data['username'];
  $userEmail = $user->data['user_email'];
 
  $date = $data['post_time']; 
  $subject = $post_data['post_subject'];

  $body = $data['message'];

  print '<p>';
  var_dump($data);
  var_dump($post_data);
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

?>
