<?php

# TODO: logging!
# TODO: Refactor postMessage().

require_once('Log.php');
$logger = &Log::singleton('file', '/var/log/listbridge', 'test2');

try {
  require_once('/var/www/bridge/src/Bridge.php');
  require_once('/var/www/bridge/src/MailmanLib.php');
  require_once('/var/www/bridge/src/MailmanMessage.php');
  require_once('/var/www/bridge/src/PhpBB3.php');
  
  # Read the message from STDIN
#  $url = 'php://stdin';
  
#  $input = read_raw_message($url);
#  $msg = new MailmanMessage($input);

  if (!isset($_POST['message'])) {
    throw new Exception('No message in POST');
  }

  $msg = new MailmanMessage($_POST['message']);
  
  $messageId = $msg->getMessageId();
  $inReplyTo = $msg->getInReplyTo();
  $rererences = $msg->getReferences();
  $soruce = $msg->getSource();
  
  $logger->info($messageId . ' received from ' . $source);

  $bridge = new Bridge();
  $editId = $bridge->registerByMessageId($messageId, $inReplyTo);
 
  if ($editId === false) {
    # This message has already been processed, bail out
    $logger->info($messageId . ' already seen, skipping');
    exit;
  }

  try {
    $phpbb = new PhpBB3();
  
    $forumId = $topicId = null;
    $postType = null;
  
    if ($inReplyTo) { 
      # Possibly a reply to an existing topic
      $parentId = $bridge->getPostId($inReplyTo);
      if ($parentId === false) {
        throw new Exception('unrecognized Reply-To: ' . $inReplyTo);
      }

      $ids = $phpbb->getTopicAndForumIds($parentId);
      if ($ids === false) {
        throw new Exception('unrecognized parent id: ' . $parentId);
      }

      # Found the parent's forum and topic, post to those
      $forumId = $ids['forum_id'];
      $topicId = $ids['topic_id'];
      $postType = 'reply';

      $logger->info($messageId . ' replies to ' . $parentId);
    }
    else {
      # A message starting a new topic, post to default forum for its source
      $forumId = $bridge->getDefaultForumId($source);
      if ($forumId === false) {
        throw new Exception('unrecognized source: ' . $source);  
      }

      $postType = 'post';

      $logger->info($messageId . ' is a new post');
    }

    $logger->info(
      $messageId . ' will be posted to ' . $forumId . ':' . $topicId);
 
    # Post the message to the forum
    $postId = $phpbb->postMessage($postType, $forumId, $topicId, $msg);
    $bridge->setPostId($messageId, $postId);

    $logger->info($messageId . ' posted as ' . $postId);
  }
  catch (Exception $e) {
    # Bridging failed, unregister message.
    $bridge->unregisterMessage($editId);
    throw $e; 
  }
}
catch (Exception $e) {
  $logger->err($e);
  error_log($e);
}

?>
