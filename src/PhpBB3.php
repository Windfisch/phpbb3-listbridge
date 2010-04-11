<?php

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
    global $db;

    $sql = 'SELECT user_id FROM ' . USERS_TABLE . ' ' .
           'WHERE user_email = "' . $db->sql_escape($from) . '"';

    $row = $this->get_exactly_one_row($sql);
    return $row['user_id'];
  }

  public function getUserName($id) {
    # NB: user_get_id_name is pass-by-reference; we copy $id to prevent
    # it from being modified, as we might need it for error messages
    $ids = array($id);
    $err = user_get_id_name($ids, $names);
    if ($err) {
      trigger_error("Could not resolve user id $id: $err", E_USER_ERROR);
    }

    if (!array_key_exists($id, $names)) {
      trigger_error("Unknown user id: $id", E_USER_ERROR);
    }

    return $names[$id];
  }

  public function getTopicAndForumIds($post_id) {
    global $db;

    $sql = 'SELECT topic_id, forum_id FROM ' . POSTS_TABLE . ' ' .
           'WHERE post_id = "' . $db->sql_escape($post_id) . '"';

    $row = $this->get_exactly_one_row($sql);
    return $row; 
  }

  public function forumExists($forumId) {
    global $db;

    if (!is_int($forumId)) {
      trigger_error('forum id is not an integer', E_USER_ERROR);
    }

    if ($forumId < 0) {
      trigger_error('forum id is negative', E_USER_ERROR);
    }

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
      trigger_error("Too many rows returned: $sql", E_USER_ERROR);
      break;
    }
  }

  public function topicExists($topicId) {
    global $db;

    if (!is_int($topicId)) {
      trigger_error('topic id is not an integer', E_USER_ERROR);
    }

    if ($topicId < 0) {
      trigger_error('topic id is negative', E_USER_ERROR);
    }

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
      trigger_error("Too many rows returned: $sql", E_USER_ERROR);
      break;
    }
  }

  public function postMessage($postType, $forumId, $topicId, $msg) {
    if ($postType != 'post' && $postType != 'reply') {
      trigger_error('bad post type: ' . $postType, E_USER_ERROR);
    }

    if (!is_int($forumId) || $forumId < 0) {
      trigger_error('bad forum id: ' . $forumId, E_USER_ERROR);
    }

    if (!$this->forumExists($forumId)) {
      trigger_error('forum does not exist: ' . $forumId, E_USER_ERROR);
    } 

    if (!is_int($topicId)) {
      trigger_error('bad topic id: ' . $topicId, E_USER_ERROR);
    }

    if ($postType == 'reply' && !$this->topicExists($topicId)) {
      trigger_error('topic does not exist: ' . $topicId, E_USER_ERROR);
    }

    if ($msg === null) {
      trigger_error('message is null', E_USER_ERROR);
    }

    $subject = $msg->getSubject(); 
    $message = 'foo'; # FIXME: fill in with acutal message contents

    $userId = $this->getUserId($msg->getFrom());
    $userName = $this->getUserName($userId);

    # authenticate ourselves
    global $phpEx, $phpbb_root_path, $user, $auth;
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
