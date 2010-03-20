<?php

require_once(__DIR__ . '/EmailMessage.php');

class MailmanMessage extends EmailMessage {
  public function __construct($input) {
    parent::__construct($input); 
  }

  public function getSource() {
    return self::parse_addr(
      substr_replace($this->msg->headers['list-post'], '', 1, 7)
    );
  }
}

?>
