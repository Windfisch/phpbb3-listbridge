<?php

require_once(__DIR__ . '/Util.php');

# phpBB setup
define('IN_PHPBB', true);
require_once(__DIR__ . '/PhpBB3Conf.php');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once($phpbb_root_path . 'common.' . $phpEx);
require_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
require_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

class PhpBB3 {
  public function __construct() {
  }

  public function getUserId($from) {
    throw_if_null($from);

    global $db;

    $sql = 'SELECT user_id FROM ' . USERS_TABLE . ' ' .
           'WHERE user_email = "' . $db->sql_escape($from) . '"';

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['user_id'] : false;
  }

  public function getUserName($id) {
    throw_if_null($id);

    # NB: user_get_id_name is pass-by-reference; we copy $id to prevent
    # it from being modified, as we might need it for error messages
    $ids = array($id);
    $err = user_get_id_name($ids, $names);
    if ($err) {
      throw new Exception("Could not resolve user id $id: $err");
    }

    if (!array_key_exists($id, $names)) {
      throw new Exception("Unknown user id: $id");
    }

    return $names[$id];
  }

  public function getTopicAndForumIds($post_id) {
    throw_if_null($post_id);

    global $db;

    $sql = 'SELECT topic_id, forum_id FROM ' . POSTS_TABLE . ' ' .
           'WHERE post_id = ' . $post_id;

    $row = $this->get_exactly_one_row($sql);
    return $row;
  }

  public function forumExists($forumId) {
    throw_if_null($forumId);

    global $db;

    $sql = 'SELECT 1 FROM ' . FORUMS_TABLE . ' ' .
           'WHERE forum_id = ' . $forumId . ' LIMIT 1';

    $result = $db->sql_query($sql);
  
    $rows = $db->sql_fetchrowset($result);
    $db->sql_freeresult($result);

    switch (count($rows)) {
    case 0:
      return false;
    
    case 1:
      return true;
    
    default:
      # Should be impossible due to LIMIT 1.
      throw new Exception("Too many rows returned: $sql");
    }
  }

  public function topicExists($topicId) {
    throw_if_null($topicId);

    global $db;

    $sql = 'SELECT 1 FROM ' . TOPICS_TABLE . ' ' .
           'WHERE topic_id = ' . $topicId . ' LIMIT 1';

    $result = $db->sql_query($sql);
  
    $rows = $db->sql_fetchrowset($result);
    $db->sql_freeresult($result);

    switch (count($rows)) {
    case 0:
      return false;
    
    case 1:
      return true;
    
    default:
      # Should be impossible due to LIMIT 1.
      throw new Exception("Too many rows returned: $sql");
    }
  }

  public function getPostTime($postId) {
    throw_if_null($postId);

    global $db;

    $sql = 'SELECT post_time FROM ' . POSTS_TABLE . ' ' .
           'WHERE post_id = ' . $postId;

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['post_time'] : false;
  }

  public function getAttachmentData($attachId) {
    throw_if_null($attachId);

    global $db;
  
    $sql = 'SELECT physical_filename, real_filename, mimetype ' .
           'FROM ' . ATTACHMENTS_TABLE .
           'WHERE attach_id = ' . $attachId;

    $row = $this->get_exactly_one_row($sql);
    return $row;
  }

  public function postMessage($postType, $forumId, $topicId, $msg) {
    throw_if_null($msg);

    if ($postType != 'post' && $postType != 'reply') {
      throw new Exception('bad post type: ' . $postType);
    }

    if (!$this->forumExists($forumId)) {
      throw new Exception('forum does not exist: ' . $forumId);
    } 

    if ($postType == 'reply' && !$this->topicExists($topicId)) {
      throw new Exception('topic does not exist: ' . $topicId);
    }

    $userId = $this->getUserId($msg->getFrom());
    if ($userId === false) {
      throw new Exception('unrecognized email address: ' . $msg->getFrom());
    }

    $userName = $this->getUserName($userId);
    if ($userName === false) {
      throw new Exception('unrecognized user id: ' . $userId);
    }

    $subject = $msg->getSubject(); 
    $message = 'foo'; # FIXME: fill in with acutal message contents

    # bring in the PhpBB globals
    global $phpEx, $phpbb_root_path, $user, $auth,
           $template, $cache, $db, $config;

    # authenticate ourselves
    $user->session_create($userId);
    $auth->acl($user->data);

    $subject = utf8_normalize_nfc($subject);
    $message = utf8_normalize_nfc($message);

    $uid = $bitfield = $options = '';

    generate_text_for_storage(
      $subject, $uid, $bitfield, $options, false, false, false
    );

    generate_text_for_storage(
      $message, $uid, $bitfield, $options, true, true, true
    );

    $postId = null;

    $data = array(
      'forum_id'         => $forumId,
      'topic_id'         => &$topicId,
      'post_id'          => &$postId,
      'icon_id'          => false,

      'enable_bbcode'    => true,
      'enable_smilies'   => true,
      'enable_urls'      => true,
      'enable_sig'       => true,

      'message'          => $message,
      'message_md5'      => md5($message),

      'bbcode_bitfield'  => $bitfield,
      'bbcode_uid'       => $uid,

      'post_edit_locked' => 0,
      'topic_title'      => $subject,
      'notify_set'       => false,
      'notify'           => false,
      'post_time'        => 0,
      'forum_name'       => '',
      'enable_indexing'  => true,
    );

    $poll = '';

    submit_post($postType, $subject, $userName, POST_NORMAL, $poll, $data);

    return $postId;
  }

  protected function get_exactly_one_row($sql) {
    global $db;

    $result = $db->sql_query($sql);
  
    $rows = $db->sql_fetchrowset($result);
    $db->sql_freeresult($result);

    switch (count($rows)) {
    case 0:
      return false;

    case 1:
      return $rows[0];

    default:
      throw new Exception("Too many rows returned: $sql");
    }
  }
}

?>
