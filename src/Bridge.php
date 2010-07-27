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

    $sql = 'SELECT p1.message_id FROM posts AS p1 ' .
           'LEFT OUTER JOIN posts AS p2 ON (' .
              'p1.post_id = p2.post_id AND ' .
              'p1.edit_id < p2.edit_id' .
           ') WHERE p1.post_id = ' . $postId . ' AND ' .
           'p2.post_id IS NULL';

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

    $sql = 'SELECT list_name FROM lists ' .
           'WHERE forum_id = ' . $forumId;

    $result = $this->db->query($sql);

    $rows = $result->fetchAll(PDO::FETCH_COLUMN);
    $result->closeCursor();
    return $rows;
  }

  public function reserveEditId($postId) {
    throw_if_null($postId);

    $sql = 'INSERT INTO posts (post_id) VALUES (' . $postId . ')';

    $count = $this->db->exec($sql);
    if ($count != 1) {
      throw new Exception('Failed to register post id: ' . $postId);
    }

    return $this->db->lastInsertId();
  }

  public function registerByEditId($editId, $messageId, $inReplyTo) {
    throw_if_null($messageId);

    $sql = 'UPDATE posts SET ' .
              'message_id = ' . $this->db->quote($messageId) . ', ' .
              'in_reply_to = ' . $this->quote($inReplyTo) . ' ' .
           'WHERE edit_id = ' . $editId;

    $count = $this->db->exec($sql);
    return $count == 1;
  }

  public function registerByMessageId($messageId, $inReplyTo) {
    throw_if_null($messageId);

    $sql = 'INSERT IGNORE INTO posts ' .
           '(message_id, in_reply_to) ' .
           'VALUES (' .
              $this->db->quote($messageId) . ', ' .
              $this->quote($inReplyTo) .
           ')';

    $count = $this->db->exec($sql);
    return $count == 1 ? $this->db->lastInsertId() : false;
  }

  public function unregisterMessage($editId) {
    throw_if_null($editId);

    $sql = 'DELETE FROM posts WHERE edit_id = ' . $editId;

    $count = $this->db->exec($sql);

    if ($count != 1) {
      throw new Exception('Failed to delete edit id: ' . $editId);
    }
  }

  public function removePost($postId) {
    throw_if_null($postId);

    $sql = 'DELETE FROM posts WHERE post_id = ' . $postId;
    
    $count = $this->db->exec($sql);

    return $count > 0;
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
