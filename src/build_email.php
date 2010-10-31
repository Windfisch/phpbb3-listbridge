<?php

require_once(__DIR__ . '/Util.php');

function build_text($text, $edit) {
  if ($edit) {
    $edit_notice = <<<EOF
[This message has been edited.]


EOF;

    $text = $edit_notice . $text;
  }

  return $text;
}

function build_footer($postId, $forumURL) {
  $postURL = "$forumURL/viewtopic.php?p=$postId#p$postId";
  $footer = <<<EOF

_______________________________________________
Read this topic online here:
$postURL
EOF;

  return $footer;
}

function build_body(array &$headers, $text, $attachments, $footer) {
  $body = null;

  # Handle attachements, if any
  if (empty($attachments)) {
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
    foreach ($attachments as $a) {
      $bytes = file_get_contents($a['path']);
      if ($bytes === false) {
        throw new Exception('failed to read file: ' . $a['path']);
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

  return $body;
}

function build_headers($userName, $userEmail, $to, $sender, $subject, $edit,
                       $time, $messageId, $forumURL, $inReplyTo, $references) {

  $from = sprintf('%s <%s>', utf8_quote_non_ascii($userName), $userEmail);
  $subject = utf8_quote_non_ascii($subject);
  $date = date(DATE_RFC2822, $time);

  if ($edit) {
    $edit_header = 'Edit: ';
    $subject = $edit_header . $subject;
  }

  $headers = array(
    'To'          => $to,
    'From'        => $from,
    'Sender'      => $sender,
    'Subject'     => $subject,
    'Date'        => $date,
    'Message-ID'  => $messageId,
    'X-BeenThere' => $forumURL
  );

  if ($inReplyTo !== null) {
    $headers['In-Reply-To'] = $inReplyTo;
  }
  
  if ($references !== null) {
    $headers['References'] = $references;
  }

  return $headers;
}

function build_text_part(Mail_mimePart $mime, $text) {
  $params = array(
    'content_type' => 'text/plain',
    'charset'      => 'utf-8',
    'encoding'     => '8bit',
    'disposition'  => 'inline'
  );
  $mime->addSubPart($text, $params);
}

function build_attachment(Mail_mimePart $mime, $type,
                          $filename, $descr, $data) {
  $params = array( 
    'content_type' => $type,
    'encoding'     => 'base64',
    'disposition'  => 'attachment',
    'dfilename'    => $filename,
    'description'  => $descr
  );
  $mime->addSubPart($data, $params);
}

function build_message_id($postId, $editId, $time, $forumHost) {
  return "<$time.$postId.$editId.bridge@$forumHost>";
}

?>