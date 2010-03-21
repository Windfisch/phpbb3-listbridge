<?php

require_once(__DIR__ . '/Message.php');

class PhpBB3Message implements Message {
  
  protected $user;
  protected $post;

  public function __construct($user, $data) {
    $this->user = $user;
    $this->post = $post;
  }

  public function getSource() {
    return null;
  }

  public function getPostId() {
    return $this->post['post_id'];
  }

  public function getFrom() {
    return '"' . $this->user->data['username'] . '" <'
               . $this->user->data['user_email'] . '>';
  }

  public function getSubject() {
    return $this->post['post_subject']; 
  }
  
  public function getMessageId() {
    return null;
  }

  public function getInReplyTo() {
    return null;
  }

  public function getReferences() {
    return null;
  }

  public function getBody() {
    return null;
  }
}

?>
