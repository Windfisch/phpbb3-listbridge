<?php

try {
  send_post_to_lists($config, $user, $mode, $data, $post_data);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

function send_post_to_lists($config, $user, $mode, $data, $post_data) {

  print '<p>';
  var_dump($data);
  var_dump($post_data);
  print '</p>';

  # Sanity check
  if (!in_array($mode, array('post', 'reply', 'quote', 'edit'))) {
    throw new Exception('unrecognized mode: ' . $mode);
  }

  require_once('Mail.php');

  require_once(__DIR__ . '/BBCodeParser.php');
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
  
  if ($mode == 'reply' || $mode == 'quote') {
    $firstId = $data['topic_first_post_id'];
    $firstMessageId = $bridge->getMessageId($firstId);
    if ($firstMessageId === false) {
      throw new Exception('unrecognized post id: ' . $firstId);
    }

    $inReplyTo = $references = $firstMessageId;
  }
  else if ($mode == 'edit') {
    $inReplyTo = $bridge->getMessageId($postId);
  }

  $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                  dirname($_SERVER['SCRIPT_NAME']);

  # Assemble the message headers
  $headers = array(
    'To'          => $to,
    'From'        => $from,
    'Subject'     => $subject,
    'Date'        => $date,
    'Message-Id'  => $messageId,
    'X-BeenThere' => $forumURL
  );

  if ($inReplyTo !== null) {
    $headers['In-Reply-To'] = $inReplyTo;
  }
  
  if ($references !== null) {
    $headers['References'] = $references;
  }

  # Build the message body
  $parser = new BBCodeParser();
  $text = $parser->parse($data['message'], $data['bbcode_uid']);

  if ($mode == 'edit') {
    $edit_notice = <<<EOF
[This message has been edited.]

EOF;

    $edit_header = 'Edit: ';

    $text = $edit_notice . $text;
    $headers['Subject'] = $edit_header . $headers['Subject'];
  }

  # Build the bridge footer
  $postURL = "$forumURL/viewtopic.php?p=$postId#p$postId";
  $footer = <<<EOF

_______________________________________________
Read this topic online here:
$postURL
EOF;

  $body = null;

  # Handle attachements, if any
  if (empty($data['attachment_data'])) {
    # No attachments, send a plain email
    $body = $text . "\n" . $footer;
    $headers['Content-Type'] = 'text/plain; charset=UTF-8; format=flowed';
    $headers['Content-Transfer-Encoding'] = '8bit';
  }
  else {
    # Attachments, build a MIME email
    require_once('Mail/mimePart.php');

    $headers['MIME-Version'] = '1.0';

    $params = array('content_type' => 'multipart/mixed');
    $mime = new Mail_mimePart('', $params);

    # Build the main body
    build_text_part($mime, $text);

    # Build each attachment
    foreach ($data['attachment_data'] as $a) {
      $attachId = $a['attach_id'];
      $adata = $phpbb->getAttachmentData($attachId);
      if ($adata === false) {
        throw new Exception('unrecognized attachment id: ' . $attachId);
      }

      $afile = $phpbb_root_path . $config['upload_path'] . '/' .
               utf8_basename($adata['physical_filename']); 

      $bytes = file_get_contents($afile);
      if ($bytes === false) {
        throw new Exception('failed to read file: ' . $afile);
      }

      build_attachment(
        $mime,
        $adata['mimetype'],
        $adata['real_filename'],
        $adata['attach_comment'],
        $bytes
      );
    }

    # Build footer
    build_text_part($mime, $footer);

    # Encode the message
    $msg = $mime->encode();
    $headers = array_merge($headers, $msg['headers']);
    $body = $msg['body'];
  }

  $mailer = Mail::factory('sendmail');

  # Register the message
  $seen = !$bridge->registerMessage($postId, $messageId, $inReplyTo);
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

# TODO: call this from handle_post_delete in posting.php
function remove_post($postId) {
  require_once(__DIR__ . '/Bridge.php');

  $bridge = new Bridge();
  $bridge->unregisterPost($postId);
}

function build_text_part($mime, $text) {
  $params = array(
    'content_type' => 'text/plain',
    'charset'      => 'utf-8',
    'encoding'     => '8bit',
    'disposition'  => 'inline'
  );
  $mime->addSubPart($text, $params);
}

function build_attachment($mime, $type, $filename, $descr, $data) {
  $params = array( 
    'content_type' => $type,
    'encoding'     => 'base64',
    'disposition'  => 'attachment',
    'dfilename'    => $filename,
    'description'  => $descr
  );
  $mime->addSubPart($data, $params);
}

?>
