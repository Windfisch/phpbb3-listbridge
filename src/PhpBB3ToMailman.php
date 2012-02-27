<?php

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
require_once('Mail.php');

require_once(__DIR__ . '/BBCodeParser.php');
require_once(__DIR__ . '/Bridge.php');
require_once(__DIR__ . '/PhpBB3.php');
require_once(__DIR__ . '/Util.php');
require_once(__DIR__ . '/build_email.php');

class PhpBB3ToMailman {

  protected $bridge;
  protected $phpbb;
  protected $mailer;
  protected $logger;

  public function __construct(Bridge $bridge, PhpBB3 $phpbb,
                              Mail $mailer, Log $logger) {
    $this->bridge = $bridge;
    $this->phpbb = $phpbb;
    $this->mailer = $mailer;
    $this->logger = $logger;
  }

  public function process($config, $user, $mode, $data, $post_data) {
    # Sanity check
    if (!in_array($mode, array('post', 'reply', 'quote', 'edit'))) {
      throw new Exception('unrecognized mode: ' . $mode);
    }

    $postId = $data['post_id'];
    $forumId = $data['forum_id'];

    $this->logger->info($postId . ' received from phpBB forum ' . $forumId);

    $to = $this->bridge->getLists($forumId);
    if (count($to) == 0) {
      # No lists to send to, bail out.
      return;    
    }
    $to = implode(', ', $to);

    $userName = $user->data['username'];
    $userEmail = $user->data['user_email'];
  
    $sender = 'forum-bridge@vassalengine.org';
  
    $subject = html_entity_decode(
      '[' . $post_data['forum_name'] . '] ' . $post_data['post_subject'],
      ENT_QUOTES
    );

    $time = null;
    if ($mode == 'edit') {
      # Post time is NOT updated on edit, so we get the current time
      $time = time();
    }
    else {
      $time = $this->phpbb->getPostTime($postId);
      if ($time === false) {
        throw new Exception('no post time: ' . $postId);
      }
    }

    $inReplyTo = null;
    $references = null;

    if ($mode == 'reply' || $mode == 'quote') {
      $firstId = $data['topic_first_post_id'];
      $firstMessageId = $this->bridge->getMessageId($firstId);
      if ($firstMessageId === false) {
        $this->logger->info($postId . ' replies to an unknown message');
      }
      else {
        $inReplyTo = $references = $firstMessageId;
        $this->logger->info($postId . ' replies to ' . $firstMessageId);
      }
    }
    else if ($mode == 'edit') {
      $inReplyTo = $this->bridge->getMessageId($postId);
    }

    $forumURL = 'http://' . $_SERVER['SERVER_NAME'] .
                    dirname($_SERVER['SCRIPT_NAME']);

    $editId = $this->bridge->reserveEditId($postId);
    $messageId = build_email_message_id($postId, $editId,
                                        $time, $_SERVER['SERVER_NAME']);

    # Assemble the message headers
    $headers = build_email_headers(
      $userName,
      $userEmail,
      $to,
      $sender,
      $subject,
      $mode == 'edit',
      $time,
      $messageId,
      $forumURL,
      $inReplyTo,
      $references
    );

    # Build the message body
    $parser = new BBCodeParser();
    $text = $parser->parse($data['message'], $data['bbcode_uid']);
    $text = build_email_text($text, $mode == 'edit');

    # Build the bridge footer
    $footer = build_email_footer($postId, $forumURL);

    $attachments = array();
    foreach ($data['attachment_data'] as $a) {
      $attachId = $a['attach_id'];

      $adata = $this->phpbb->getAttachmentData($attachId);
      if ($adata === false) {
        throw new Exception('unrecognized attachment id: ' . $attachId);
      }

      $adata['path'] = $phpbb_root_path . $config['upload_path'] . '/' .
                       utf8_basename($adata['physical_filename']);

      $attachments[] = $adata;
    }

    # Build the message body
    $body = build_email_body($headers, $text, $attachments, $footer);

    # Register the message
    $seen = !$this->bridge->registerByEditId($editId, $messageId, $inReplyTo);
    if ($seen) {
      throw new Exception('message id already seen: ' . $messageId);
    }

    try {
      # Send the message
      $err = $this->mailer->send($to, $headers, $body);
      if (PEAR::isError($err)) {
        throw new Exception('Mail::send error: ' . $err->toString());
      }

      $this->logger->info($postId . ' sent to ' . $to . ' as ' . $messageId);
    }
    catch (Exception $e) {
      # Bridging failed, unregister message.
      $this->bridge->unregisterMessage($editId);
      throw $e;
    }
  }
}

?>
