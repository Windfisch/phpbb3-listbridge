<?php

require_once(__DIR__ . '/BridgeConf.php');

class Bridge {
  protected $db;

  public function __construct($db = FALSE) {
    $this->db = $db ? $db :
      new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);
  }

  public function getPostId($messageId) {
    $sql = 'SELECT post_id FROM posts' .
           'WHERE message_id = "' . $this->db->quote($message_id) . '"';

    $result = $this->db->query($sql);
    if (!$result) {
      trigger_error("Unknown message id: $message_id", E_USER_ERROR);
    }

    // FIXME: what to do if more than one row is returned?
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $result->closeCursor();

    return $row['post_id'];
  }

  public function registerMessage($msg, $parentId) {
    $sql = 'INSERT INTO posts' .
           '(post_id, message_id, parent_message_id, references)' .
           'VALUES('   . $msg->getPostId()                       . ', '
                   '"' . $this->db->quote($msg->getMessageId())  . '", '
                   '"' . $this->db->quote()                      . '", ' 
                   '"' . $this->db->quote($msg->getReferences()) . '")'; 

    $count = $this->db->exec($sql);

    if ($count != 1) {
      trigger_error(
        'Failed to register message: ' . $msg->getMessageId(), E_USER_ERROR
      );
    }
  }

  public function getDefaultForumId($list) {
    $sql = 'SELECT forum_id FROM bridge' .
           'WHERE list_name = "' . $this->db->quote($list) . '"';

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
