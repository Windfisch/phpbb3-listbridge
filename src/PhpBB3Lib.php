<?php

# phpBB setup
define('IN_PHPBB', true);
require_once(__DIR__ . '/PhpBB3Conf.php');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once($phpbb_root_path . 'common.' . $phpEx);
require_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);


function get_user_id($from) {
  global $db;

  $sql = 'SELECT user_id FROM ' . USERS_TABLE .
         ' WHERE user_email = "' . $db->sql_escape($from) . '"';

  $result = $db->sql_query($sql);
// FIXME: what to do if more than one row is returned?
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if (!$row) {
    trigger_error("Unknown user email: $from", E_USER_ERROR);
  }

  return $row['user_id'];
}

function get_user_name($id) {
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

function get_topic_id($post_id) {
  global $db;

// FIXME: should get topic_id, forum_id at the same time
  $sql = 'SELECT topic_id FROM ' . POSTS_TABLE .
         ' WHERE post_id = "' . $db->sql_escape($post_id) . '"';

  $result = $db->sql_query($sql);
// FIXME: what to do if more than one row is returned?
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if (!$row) {
    trigger_error("Unknown post id: $post_id", E_USER_ERROR);
  }

  return $row['topic_id'];
}

?>
