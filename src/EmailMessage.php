<?php

# FIXME: maybe use Mailparse instead of Mail_mimeDecode

require_once('Mail/mimeDecode.php');
require_once('Mail/RFC822.php');

require_once(__DIR__ . '/Message.php');

abstract class EmailMessage implements Message {

  protected $msg;

  public function __construct($input) {
    $this->msg = self::decode_raw_message($input);
  }

  public function getPostId() {
    return null;
  }

  public function getFrom() {
    return self::parse_addr($this->msg->headers['from']);
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
    return isset($this->msg->body) ? $this->msg->body : false;
  }

  public function getParts() {
    return $this->msg->parts;
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
      throw new Exception('No message');
    }

    return $msg;
  }

  protected static function parse_addr($s) {
    $addr = Mail_RFC822::parseAddressList($s);
    return strtolower($addr[0]->mailbox . '@' . $addr[0]->host);
  }
}

?>
