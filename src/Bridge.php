<?php

require_once(__DIR__ . '/BridgeConf.php');

class Bridge {
  protected $db;

  public function __construct($db = FALSE) {
    $this->db = $db ? $db :
      new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);

    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function getPostId($messageId) {
    $sql = 'SELECT post_id FROM posts ' .
           'WHERE message_id = ' . $this->db->quote($messageId);

    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    $result->closeCursor();

    switch (count($rows)) {
    case 0:
      trigger_error("Unknown message id: $messageId", E_USER_ERROR);
      break;

    case 1:
      return $rows[0]['post_id'];

    default:
      trigger_error("Too many rows returned: $messageId", E_USER_ERROR);
      break;
    }   
  }

  public function getMessageId($postId) {
    $sql = 'SELECT message_id FROM posts ' .
           'WHERE post_id = ' . $this->db->quote($postId);

    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    $result->closeCursor();
    
    switch (count($rows)) {
    case 0:
      trigger_error("Unknown post id: $postId", E_USER_ERROR);
      break;

    case 1:
      return $rows[0]['message_id'];

    default:
      trigger_error("Too many rows returned: $postId", E_USER_ERROR);
      break;
    }    
  }

  public function registerMessage($msg, $parentId) {
    $sql = 'INSERT INTO posts ' .
           '(post_id, message_id, in_reply_to, refs) ' .
           'VALUES (' . $msg->getPostId()                       . ', '
                      . $this->db->quote($msg->getMessageId())  . ', '
                      . $this->db->quote()                      . ', '
                      . $this->db->quote($msg->getReferences()) . ')'; 

    $count = $this->db->exec($sql);

    if ($count != 1) {
      trigger_error(
        'Failed to register message: ' . $msg->getMessageId(), E_USER_ERROR
      );
    }
  }

  public function getDefaultForumId($list) {
    $sql = 'SELECT forum_id FROM bridge ' .
           'WHERE list_name = ' . $this->db->quote($list);

    $result = $this->db->query($sql);
    if (!$result) {
      trigger_error("Unknown list name: $list", E_USER_ERROR);
    }

    // FIXME: what to do if more than one row is returned?
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $result->closeCursor();

    return $row['forum_id'];
  }
}

?>
