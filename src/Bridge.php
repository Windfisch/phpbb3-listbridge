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

    $row = $this->get_exactly_one_row($sql);
    return $row['post_id'];
  }

  public function getMessageId($postId) {
    $sql = 'SELECT message_id FROM posts ' .
           'WHERE post_id = ' . $this->db->quote($postId);

    $row = $this->get_exactly_one_row($sql);
    return $row['message_id'];
  }

  public function setPostId($messageId, $postId) {
    $sql = 'UPDATE posts SET ' .
           'post_id = ' . $postId . ' ' . 
           'WHERE message_id = ' . $this->db->quote($messageId);

    $count = $this->db->exec($sql);

    if ($count != 1) {
      trigger_error(
        'Failed to set post id: ' . $messageId, E_USER_ERROR
      );
    }
  }

  public function getDefaultForumId($list) {
    $sql = 'SELECT forum_id FROM forums ' .
           'WHERE list_name = ' . $this->db->quote($list);

    $row = $this->get_exactly_one_row($sql);
    return $row['forum_id'];
  }

  public function registerMessage($messageId, $inReplyTo, $refs) {
    if ($messageId === null) {
      trigger_error('message id is null', E_USER_ERROR);
    } 

    $sql = 'INSERT IGNORE INTO posts ' .
           '(message_id, in_reply_to, refs) ' .
           'VALUES (' . $this->db->quote($messageId) . ', '
                      . $this->db->quote($inReplyTo) . ', '
                      . $this->db->quote($refs)      . ')'; 

    $count = $this->db->exec($sql);

    return $count == 1;
  }

  protected function get_exactly_one_row($sql) {
    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    $result->closeCursor();
    
    switch (count($rows)) {
    case 0:
      trigger_error("No rows returned: $sql", E_USER_ERROR);
      break;

    case 1:
      return $rows[0];

    default:
      trigger_error("Too many rows returned: $sql", E_USER_ERROR);
      break;
    }
  }
}

?>
