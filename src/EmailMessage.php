<?php

require_once('Mail/mimeDecode.php');
require_once('Mail/RFC822.php');

require_once('Message.php');

class EMailMessage implements Message {

  protected $msg;

  public function __construct($input) {
    $this->msg = EMailMessage::decode_raw_message($input);
  }

  public function getSource() {
# FIXME: fill in!
    return null;
  }

  public function getPostId() {
# FIXME: get from message-id to post-id database
    return null;
  }

  public function getFrom() {
    return EMailMessage::parse_addr($this->msg->headers['from']);
  }

  public function getSubject() {
    return $this->msg->headers['subject'];
  }
  
  public function getMessageId() {
    return $this->msg->headers['message-id'];
  }

  public function getInReplyTo() {
    return $this->msg->headers['in-reply-to'];
  }

  public function getReferences() {
    return $this->msg->headers['references'];
  }

  public function getBody() {
    return $this->msg->body;
  }

  protected static function decode_raw_message($input) {
    $params['include_bodies'] = true;
    $params['decode_bodies']  = true;
    $params['decode_headers'] = true;
    $params['input']          = $input;
    $params['crlf']           = "\r\n";

    $msg = Mail_mimeDecode::decode($params);

    if (count($msg->headers) == 1 && array_key_exists(null, $msg->headers)) {
      # An empty message has one null header.
      trigger_error('No message', E_USER_ERROR);
    }

    return $msg;
  }

  protected static function parse_addr($s) {
    $addr = Mail_RFC822::parseAddressList($s);
    return strtolower($addr[0]->mailbox . '@' . $addr[0]->host);
  }
}

?>
