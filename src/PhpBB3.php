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

    $sql = 'SELECT user_id FROM ' . USERS_TABLE .
           ' WHERE user_email = "' . $db->sql_escape($from) . '"';

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

    $sql = 'SELECT topic_id, forum_id FROM ' . POSTS_TABLE .
           ' WHERE post_id = "' . $db->sql_escape($post_id) . '"';

    $row = get_exactly_one_row($sql);
    return $row; 
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
