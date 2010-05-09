<?php

require_once(__DIR__ . '/Util.php');

# phpBB setup
define('IN_PHPBB', true);
require_once(__DIR__ . '/PhpBB3Conf.php');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once($phpbb_root_path . 'common.' . $phpEx);
require_once($phpbb_root_path . 'includes/functions.' . $phpEx);
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

  public function topicStatus($topicId) {
    throw_if_null($topicId);

    global $db;

    $sql = 'SELECT topic_status FROM ' . TOPICS_TABLE . ' ' .
           'WHERE topic_id = ' . $topicId;

    $row = $this->get_exactly_one_row($sql);
    return $row ? $row['topic_status'] : false;
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
  
    $sql = 'SELECT physical_filename, real_filename, ' . 
                  'attach_comment, mimetype ' .
           'FROM ' . ATTACHMENTS_TABLE . ' ' .
           'WHERE attach_id = ' . $attachId;

    $row = $this->get_exactly_one_row($sql);
    return $row;
  }

  public function postMessage($postType, $forumId, $topicId, $msg) {
    throw_if_null($msg);

    if ($postType == 'post') {
      # do nothing
    }
    else if ($postType == 'reply') {
      # Check that we're not replying to a locked topic.
      $status = $this->topicStatus($topicId);
      if ($status === false) {
        throw new Exception('topic does not exist: ' . $topicId);
      }

      switch ($this->topicStatus($topicId)) {
      case ITEM_UNLOCKED:
        # normal, ok
        break;
      case ITEM_LOCKED:
        throw new Exception('post to locked topic: ' . $topicId); 
        break;
      case ITEM_MOVED:
        # Should not happen, since the only topics with this status
        # are new shadow topics created after moves.
        throw new Exception('post to moved topic: ' . $topicId);
        break;
      default:
        # Should not happen.
        throw new Exception('bad topic status: ' . $topicId);
        break;
      }
    }
    else {
      # Should not happen.
      throw new Exception('bad post type: ' . $postType);
    }

    if (!$this->forumExists($forumId)) {
      throw new Exception('forum does not exist: ' . $forumId);
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
    list($message, $attachments) = $msg->getFlattenedParts();

    # handle attachments
    $attachment_data = array();

    foreach ($attachments as $a) {
      $attachment_data[] = $this->addAttachment(
        $userId, $a['filename'], $a['comment'], $a['mimetype'], $a['data']
      );
    } 

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

    # build the data array for submit_post
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
      'enable_indexing'  => true
    );

    if (!empty($attachment_data)) {
      $data['attachment_data'] = $attachment_data;
    }

    $poll = '';

    submit_post($postType, $subject, $userName, POST_NORMAL, $poll, $data);

    return $postId;
  }

  public function addAttachment($userId, $filename, $comment,
                                                    $mimetype, $data) {
    throw_if_null($userId);
    throw_if_null($filename);
    throw_if_null($mimetype);
    throw_if_null($data);

# TODO: check that attachment is a permissible type, size

    # lifted from include/functions_upload.php: filespec::clean_filename()
    $realFilename = $userId . '_' . md5(unique_id()); 

    # put the attachment data into the db
    $sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' (' .
             'poster_id, is_orphan, physical_filename, attach_comment, ' .
             'extension, mimetype, filesize, filetime' .
           ') VALUES (' .
             $userId . ', ' .
             '1, ' .
             $realFilename . ', ' .
             $comment . ', ' .
             $mimetype . ', ' .
             strlen($data) . ', ' .
             time() .
           ')';

    $db->sql_query($sql);

    if ($db->sql_affectedrows() != 1) {
      throw new Exception("Adding attachment failed: $sql");
    }

    # write the attachment to disk
    $realPath = $phpbb_root_path . $config['upload_path'] . '/' . $realFilename;
    $count = file_put_contents($realPath, $data);
    if ($count === false) {
      throw new Exception('Failed to write attachment file: ' . $realPath);
    }

# FIXME: how to get right uid, gid for file? This surely won't work.
/*
    $result = chown($realPath, 'apache');
    if ($result === false) {
      throw new Exception('Failed to chown attachment file: ' . $realPath);
    }

    chgrp($realPath, 'apache');
    if ($result === false) {
      throw new Exception('Failed to chgrp attachment file: ' . $realPath);
    }
*/

    # return the attachment info needed by submit_post
    return array(
      'attach_id'      => $db->sql_nextid(),
      'is_orphan'      => 1,
      'real_filename'  => $realFilename,
      'attach_comment' => $comment,
    );
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
