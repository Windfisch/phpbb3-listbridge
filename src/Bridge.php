<?php

require_once(__DIR__ . '/BridgeConf.php');
require_once(__DIR__ . '/Util.php');

class Bridge {
  protected $db;

  public function __construct($db = FALSE) {
    $this->db = $db ? $db :
      new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);

    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function getPostId($messageId) {
    throw_if_null($messageId);

    $sql = 'SELECT post_id FROM posts ' .
           'WHERE message_id = ' . $this->db->quote($messageId);

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['post_id'] : false;
  }

  public function getMessageId($postId) {
    throw_if_null($postId);

    $sql = 'SELECT message_id FROM posts ' .
           'WHERE post_id = ' . $this->db->quote($postId);

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['message_id'] : false;
  }

  public function setPostId($messageId, $postId) {
    throw_if_null($messageId);
    throw_if_null($postId);

    $sql = 'UPDATE posts SET ' .
           'post_id = ' . $postId . ' ' . 
           'WHERE message_id = ' . $this->db->quote($messageId);

    $count = $this->db->exec($sql);

    if ($count != 1) {
      throw new Exception('Failed to set post id: ' . $messageId);
    }
  }

  public function getDefaultForumId($list) {
    throw_if_null($list);

    $sql = 'SELECT forum_id FROM forums ' .
           'WHERE list_name = ' . $this->db->quote($list);

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['forum_id'] : false;
  }

  public function getLists($forumId) {
    throw_if_null($forumId);

    $sql = 'SELECT list_name FROM forums ' .
           'WHERE forum_id = ' . $forumId;

    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_COLUMN, 0);
    $result->closeCursor();
    return $rows;
  }

  public function registerMessage($messageId, $inReplyTo, $references) {
    throw_if_null($messageId);

    $sql = 'INSERT IGNORE INTO posts ' .
           '(message_id, in_reply_to, refs) ' .
           'VALUES (' . $this->db->quote($messageId) . ', '
                      . $this->quote($inReplyTo) . ', '
                      . $this->quote($references) . ')'; 

    $count = $this->db->exec($sql);
    return $count == 1;
  }

  public function unregisterMessage($messageId) {
    throw_if_null($messageId);

    $sql = 'DELETE FROM posts WHERE message_id = ' .
           $this->db->quote($messageId);

    $count = $this->db->exec($sql);

    if ($count != 1) {
      throw new Exception('Failed to delete message id: ' . $messageId);
    }
  }

  protected function get_exactly_one_row($sql) {
    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    $result->closeCursor();
    
    switch (count($rows)) {
    case 0:
      return false;

    case 1:
      return $rows[0];

    default:
      throw new Exception("Too many rows returned: $sql");
    }
  }

  protected function quote($arg) {
    return $arg === null ? 'NULL' : $this->db->quote($arg);
  }
}

?>
