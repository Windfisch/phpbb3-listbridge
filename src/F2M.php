<?php

try {
  send_to_lists($user, $mode, $data, $post_data);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

# TODO: wrap long lines
# TODO: handle attachments

function send_to_lists($user, $mode, $data, $post_data) {

/*
  print '<p>';
  var_dump($data);
  var_dump($post_data);
  print '</p>';
*/

/*
  $text = nl2br($data['message']);
  $bbcode = new bbcode(base64_encode($data['bbcode_bitfield']));         
  $bbcode->bbcode_second_pass($text, $data['bbcode_uid'], $data['bbcode_bitfield']);

  print $text;
*/

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

  $inReplyTo = null;
  $references = null;
  
  if ($mode == 'reply') {
    $firstId = $data['topic_first_post_id']; 
    $firstMessageId = $bridge->getMessageId($firstId);
    if ($firstMessageId === null) {
      throw new Exception('unrecognized post id: ' . $firstId);
    }

# FIXME: try to build better References by matching, maybe?
    $inReplyTo = $references = $firstMessageId;
  }

  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                  dirname($_SERVER['SCRIPT_NAME']);

  $body = $data['message'];

  # Assemble the message headers
  $headers = array(
    'To'                       => $to,
    'From'                     => $from,
    'Subject'                  => $subject,
    'Date'                     => $date,
    'Message-Id'               => $messageId,
    'X-BeenThere'              => $forumURL,
    'Content-Type'             => 'text/plain; charset=UTF-8; format=flowed',
    'MIME-Version'             => '1.0',
    'Conten-Transfer-Encoding' => '8bit'
  );

  if ($inReplyTo !== null) {
    $headers['In-Reply-To'] = $inReplyTo;
  }
  
  if ($references !== null) {
    $headers['References'] = $references;
  }

  $mailer = Mail::factory('sendmail');

  $seen = !$bridge->registerMessage($postId, $messageId,
                                    $inReplyTo, $references);
  if ($seen) {
    throw new Exception('message id already seen: ' . $messageId);
  }

  try {
    # Send the message
    $err = $mailer->send($to, $headers, $body);
    if (PEAR::isError($err)) {
      throw new Exception('Mail::send error: ' . $err->toString());
    }
  }
  catch (Exception $e) {
    # Bridging failed, unregister message.
    $bridge->unregisterMessage($messageId);
    throw $e;
  }
}

?>
