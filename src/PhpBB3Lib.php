<?php

# phpBB setup
define('IN_PHPBB', true);
require_once(__DIR__ . '/PhpBB3Conf.php');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once($phpbb_root_path . 'common.' . $phpEx);
#require_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);


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


?>
