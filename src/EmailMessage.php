<?php

#
# $Id$
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

# FIXME: maybe use Mailparse instead of Mail_mimeDecode

require_once('Mail/mimeDecode.php');
require_once('Mail/RFC822.php');

require_once(__DIR__ . '/Message.php');

abstract class EmailMessage implements Message {

  protected $msg;

  public function __construct($input) {
    $this->msg = self::decode_raw_message($input);
  }

  public function getPostId() {
    return null;
  }

  public function getFrom() {
    return self::parse_addr($this->msg->headers['from']);
  }

  public function getSubject() {
    return $this->msg->headers['subject'];
  }
  
  public function getMessageId() {
    return $this->msg->headers['message-id'];
  }

  public function getInReplyTo() {
    return $this->msg->headers['in-reply-to'];
  }

  public function getReferences() {
    return $this->msg->headers['references'];
  }

  public function getParts() {
    return $this->msg;
  }

  public function getFlattenedParts() {
    $text = '';
    $attachments = array();
    self::flatten_parts($this->msg, $text, $attachments);
    return array($text, $attachments);
  }

  protected static function decode_raw_message($input) {
    $params['include_bodies'] = true;
    $params['decode_bodies']  = true;
    $params['decode_headers'] = true;
    $params['input']          = $input;
    $params['crlf']           = "\r\n";

    $msg = Mail_mimeDecode::decode($params);

    if (count($msg->headers) == 1 && array_key_exists(null, $msg->headers)) {
      # An empty message has one null header.
      throw new Exception('No message');
    }

    return $msg;
  }

  protected static function parse_addr($s) {
    $addr = Mail_RFC822::parseAddressList($s);
    return strtolower($addr[0]->mailbox . '@' . $addr[0]->host);
  }

  protected static function flatten_parts($part, &$text, &$attachments) {
    switch ($part->ctype_primary) {
    case 'multipart':
      if (!isset($part->parts)) {
        throw new Exception('multipart without parts!');
      }

      foreach ($part->parts as $subpart) {
        self::flatten_parts($subpart, $text, $attachments);
      }
      break;

    case 'text':
      # text/* parts go into the message body.
      if (!isset($part->body)) {
        throw new Exception('text without body!');
      }

      $text .= $part->body; 
      break;

    default:
      # Everything else goes into phpBB as an attachment.
      if (!isset($part->body)) {
        throw new Exception('attachment without body!');
      }

      # try to find a filename
      $filename = '';
      if (isset($part->d_parameters)) {
        if (array_key_exists('filename', $part->d_parameters)) {
          $filename = $part->d_parameters['filename'];
        }
        else if (array_key_exists('name', $part->d_parameters)) {
          $filename = $part->d_parameters['name'];
        }
      }

      if ($filename == '') {
        if (isset($part->ctype_parameters)) {
          if (array_key_exists('name', $part->ctype_parameters)) {
            $filename = $part->d_parameters['name'];
          }
        }
      }

      $mimetype = $part->ctype_primary . '/' . $part->ctype_secondary;

      $comment = array_key_exists('content-description', $part->headers) ?
        $part->headers['content-description'] : '';

      $params = array(
        'filename' => $filename,
        'mimetype' => $mimetype,
        'comment'  => $comment,
        'data'     => $part->body
      );

      $attachments[] = $params;
    }
  }
}

?>
