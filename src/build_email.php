<?php

require_once(__DIR__ . '/Util.php');

function build_email_text($text, $edit) {
  if ($edit) {
    $edit_notice = <<<EOF
[This message has been edited.]


EOF;

    $text = $edit_notice . $text;
  }

  return $text;
}

function build_email_footer($postId, $forumURL) {
  $postURL = "$forumURL/viewtopic.php?p=$postId#p$postId";
  $footer = <<<EOF

_______________________________________________
Read this topic online here:
$postURL
EOF;

  return $footer;
}

function build_email_body(array &$headers, $text, $attachments, $footer) {
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
    build_email_text_part($mime, $text);

    # Build each attachment
    foreach ($attachments as $a) {
      $bytes = file_get_contents($a['path']);
      if ($bytes === false) {
        throw new Exception('failed to read file: ' . $a['path']);
      }

      build_email_attachment(
        $mime,
        $adata['mimetype'],
        $adata['real_filename'],
        $adata['attach_comment'],
        $bytes
      );
    }

    # Build footer
    build_email_text_part($mime, $footer);

    # Encode the message
    $msg = $mime->encode();
    $headers = array_merge($headers, $msg['headers']);
    $body = $msg['body'];
  }

  return $body;
}

function build_email_from($name, $email) {
  $qname = ''; 

  if (is_ascii($name)) {
    if (has_rfc822_specials($name)) {
      $qname = rfc822_quote($name);
    }
    else {
      $qname = $name;
    }
  }
  else {
    // base64-encode if we have non-ASCII chars
    $qname = utf8_quote($name);
  }

  return sprintf('%s <%s>', $qname, $email);
}

function build_email_subject($forumtag, $subject, $reply, $edit) {
  $subject = trim($subject);
  if ($subject == '') {
    $subject = '(no subject)';
  }

  $subject = $forumtag . ' ' . $subject;

  if ($reply) {
    $subject = 'Re: ' . $subject;
  }

  if ($edit) {
    $subject = 'Edit: ' . $subject;
  }

  return utf8_quote_non_ascii($subject);
}

function build_email_headers(
  $userName, $userEmail, $to, $sender, $subject, $time,
  $messageId, $forumURL, $inReplyTo, $references)
{
  $from = build_email_from($userName, $userEmail); 
  $subject = utf8_quote_non_ascii($subject);
  $date = date(DATE_RFC2822, $time);

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

function build_email_text_part(Mail_mimePart $mime, $text) {
  $params = array(
    'content_type' => 'text/plain',
    'charset'      => 'utf-8',
    'encoding'     => '8bit',
    'disposition'  => 'inline'
  );
  $mime->addSubPart($text, $params);
}

function build_email_attachment(Mail_mimePart $mime, $type,
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

function build_email_message_id($postId, $editId, $time, $forumHost) {
  return "<$time.$postId.$editId.bridge@$forumHost>";
}

?>
