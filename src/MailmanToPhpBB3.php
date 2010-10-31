<?php

#
# $Id: Bridge.php 7051 2010-07-29 13:23:57Z uckelman $
#
# forum-list bridge 
# Copyright (C) 2010 Joel Uckelman
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.
#

require_once('Log.php');

require_once(__DIR__ . '/Bridge.php');
require_once(__DIR__ . '/Message.php');
require_once(__DIR__ . '/PhpBB3.php');

class MailmanToPhpBB3 {
  protected $bridge;
  protected $phpbb;
  protected $logger;

  public function __construct(Bridge $bridge, PhpBB3 $phpbb, Log $logger) {
    $this->bridge = $bridge;
    $this->phpbb = $phpbb;
    $this->logger = $logger;
  }

  public function process(Message $msg) {
    $messageId = $msg->getMessageId();
    $inReplyTo = $msg->getInReplyTo();
    $rererences = $msg->getReferences();
    $source = $msg->getSource();
  
    $this->logger->info($messageId . ' received from ' . $source);

    $editId = $this->bridge->registerByMessageId($messageId, $inReplyTo);

    if ($editId === false) {
      # This message has already been processed, bail out
      $this->logger->info($messageId . ' already seen, skipping');
      exit;
    }

    try {
      $forumId = $topicId = null;
      $postType = null;
  
      if ($inReplyTo) { 
        # Possibly a reply to an existing topic
        $parentId = $this->bridge->getPostId($inReplyTo);
        if ($parentId === false) {
          throw new Exception('unrecognized Reply-To: ' . $inReplyTo);
        }

        $ids = $this->phpbb->getTopicAndForumIds($parentId);
        if ($ids === false) {
          throw new Exception('unrecognized parent id: ' . $parentId);
        }

        # Found the parent's forum and topic, post to those
        $forumId = $ids['forum_id'];
        $topicId = $ids['topic_id'];
        $postType = 'reply';

        $this->logger->info($messageId . ' replies to ' . $parentId);
      }
      else {
        # A message starting a new topic, post to default forum for its source
        $forumId = $this->bridge->getDefaultForumId($source);
        if ($forumId === false) {
          throw new Exception('unrecognized source: ' . $source);  
        }

        $postType = 'post';

        $this->logger->info($messageId . ' is a new post');
      }

      $this->logger->info(
      $messageId . ' will be posted to ' . $forumId . ':' . $topicId);
 
      # Post the message to the forum
      $postId = $this->phpbb->postMessage($postType, $forumId, $topicId, $msg);
      $this->bridge->setPostId($messageId, $postId);

      $this->logger->info($messageId . ' posted as ' . $postId);
    }
    catch (Exception $e) {
      # Bridging failed, unregister message.
      $this->bridge->unregisterMessage($editId);
      throw $e; 
    }
  }
}

?>
