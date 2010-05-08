<?php

interface Message {
  public function getSource();

  public function getPostId();

  public function getFrom();

  public function getSubject();
  
  public function getMessageId();

  public function getInReplyTo();

  public function getReferences();

  public function getBody();

  public function getParts();
}

?>
