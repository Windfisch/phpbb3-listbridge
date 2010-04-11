<?php

require_once(__DIR__ . '/Bridge.php');
require_once(__DIR__ . '/MailmanLib.php');
require_once(__DIR__ . '/MailmanMessage.php');
require_once(__DIR__ . '/PhpBB3.php');

# Read the message from STDIN
$url = 'php://stdin';

$input = read_raw_message($url);
$msg = new MailmanMessage($input);

$bridge = new Bridge();

$seen = $bridge->registerMessage($msg->getMessageId(),
                                 $msg->getInReplyTo(),
                                 $msg->getReferences());

if ($seen) {
  # This message has already been processed.
  exit;
}

$phpbb = new PhpBB3();

$inReplyTo = $msg->getInReplyTo();

$forumId = $topicId = -1;
$postType = null;

if ($inReplyTo) { 
  # A reply to an existing topic
# FIXME: we don't want exceptions here?
  $parentId = $bridge->getPostId($inReplyTo);
  $ids = $phpbb->getTopicAndForumIds($parentId);
  $forumId = $ids['forum_id'];
  $topicId = $ids['topic_id'];
  $postType = 'reply';
}
else {
  # A message starting a new topic
  $forumId = $bridge->getDefaultForumId($msg->getSource());
  $postType = 'post';
}

# Post the message to the forum
$phpbb->postMessage($postType, $forumId, $topicId, $msg);
$bridge->setPostId($messageId, $postId);

?>
