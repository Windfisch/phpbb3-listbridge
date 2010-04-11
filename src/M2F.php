<?php

require_once(__DIR__ . '/Bridge.php');
require_once(__DIR__ . '/MailmanLib.php');
require_once(__DIR__ . '/MailmanMessage.php');
require_once(__DIR__ . '/PhpBB3Lib.php');

# Read the message from STDIN
$url = 'php://stdin';

$input = read_raw_message($url);
$msg = new MailmanMessage($input);

$user = get_user_id($msg->getFrom());
$userName = get_user_name($userId);

$bridge = new Bridge();

$inReplyTo = $msg->getInReplyTo();

if ($inReplyTo) { 
  # Is this a reply?
# FIXME: we don't want exceptions here?
  $parentId = $bridge->getPostId($inReplyTo);
  $topicId = get_topic_id($parentId);
}
else {
  # a new message

}

#$forumId = get_default_forum_id($msg->getSource());

?>
