<?php

# TODO: logging!
# TODO: Refactor postMessage().

try {
  require_once(__DIR__ . '/Bridge.php');
  require_once(__DIR__ . '/MailmanLib.php');
  require_once(__DIR__ . '/MailmanMessage.php');
  require_once(__DIR__ . '/PhpBB3.php');
  
  # Read the message from STDIN
  $url = 'php://stdin';
  
  $input = read_raw_message($url);
  $msg = new MailmanMessage($input);
  
  $messageId = $msg->getMessageId();
  $inReplyTo = $msg->getInReplyTo();
  $rererences = $msg->getReferences();
  
  $bridge = new Bridge();
  $editId = $bridge->registerByMessageId($messageId, $inReplyTo);
  
  try {
    if ($editId === false) {
      # This message has already been processed, bail out
      print 'Message id already seen, skipping: ' . $messageId . "\n";
      exit;
    }
  
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
    }
    else {
      # A message starting a new topic, post to default forum for its source
      $forumId = $bridge->getDefaultForumId($msg->getSource());
      if ($forumId === false) {
        throw new Exception('unrecognized source: ' . $msg->getSource());  
      }

      $postType = 'post';
    }
 
    # Post the message to the forum
    $postId = $phpbb->postMessage($postType, $forumId, $topicId, $msg);
    $bridge->setPostId($messageId, $postId);
  }
  catch (Exception $e) {
    # Bridging failed, unregister message.
    $bridge->unregisterMessage($editId);
    throw $e; 
  }
}
catch (Exception $e) {
  print "$e\n";
}

?>
