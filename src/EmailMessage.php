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

require_once(__DIR__ . '/Message.php');

abstract class EmailMessage implements Message {

  protected $data;
  protected $msg;
  protected $parts;
  
  public function __construct($input) {
    $this->data = &$input;

    # build the message structure
    $this->msg = mailparse_msg_create();
    mailparse_msg_parse($this->msg, $this->data);

    # get the part data
    $this->parts = array();
    foreach (mailparse_msg_get_structure($this->msg) as $part_id) {
      $part = mailparse_msg_get_part($this->msg, $part_id);
      $this->parts[$part_id] = mailparse_msg_get_part_data($part);
    }
  }

  public function __destruct() {
    mailparse_msg_free($this->msg);
  }

  public function getPostId() {
    return null;
  }

  protected function getHeader($name) {
    return $this->getPartHeader($this->parts[1], $name);
  }

  protected function getPartHeaders(&$part_data) {
    $headers = &$part_data['headers'];
    return $headers;
  }

  protected function getPartHeader(&$part_data, $name) {
    $headers = $this->getPartHeaders($part_data);
    return array_key_exists($name, $headers) ? $headers[$name] : false;
  }

  public function getFrom() {
    $from = mailparse_rfc822_parse_addresses($this->getHeader('from'));
    return $from[0]['address'];
  }

  public function getSubject() {
    return $this->getHeader('subject');
  }
  
  public function getMessageId() {
    return $this->getHeader('message-id');
  }

  public function getInReplyTo() {
    $irt = $this->getHeader('in-reply-to');

    // Try to get a message id from the In-Reply-To header
    foreach (mailparse_rfc822_parse_addresses($irt) as $part) {
      if (isset($part['address']) && strlen($part['address']) > 0) {
        return '<' . $part['address'] . '>';
      }
    }
  
    // Ack, no message id, just return the raw header
    return $irt; 
  }

  public function getReferences() {
    return $this->getHeader('references');
  }

  public function getParts() {
  }

  protected function getPartBody(&$part_data) {
    $beg = $part_data['starting-pos-body'];
    $end = $part_data['ending-pos-body'];
    return substr($this->data, $beg, $end-$beg);
  }

  protected function decode($str, $encoding) {
    if ($encoding == 'base64') {
      return base64_decode($str);
    }
    else if ($encoding == 'quoted-printable') {
      return quoted_printable_decode($str);
    }
    else {
      return $str;
    }
  }

  public function getFlattenedParts() {
    $text = '';
    $attachments = array();
    $this->flatten_parts('1', $this->parts[1], $text, $attachments);
    return array($text, $attachments);
  }

  protected function flatten_subparts($part_id, &$text, &$attachments) {
    for ($i = 1, $child_id = "$part_id.$i";
         array_key_exists($child_id, $this->parts);
         ++$i, $child_id = "$part_id.$i")
    {
      $child = $this->parts[$child_id];
      $this->flatten_parts($child_id, $child, $text, $attachments);
    }
  }

  protected function flatten_parts($part_id, &$part_data,
                                   &$text, &$attachments) {
    $type = $part_data['content-type'];
    list($major, $minor) = split('/', $type, 2);

    switch ($major) {
    case 'multipart':
      switch ($minor) {
      case 'alternative':
        # check alternatives for text/plain
        $plain = false;

        for ($i = 1, $child_id = "$part_id.$i";
             array_key_exists($child_id, $this->parts);
             ++$i, $child_id = "$part_id.$i")
        {
          $child = $this->parts[$child_id];

          $ctype = $child['content-type'];
          if ($ctype == 'text/plain') {
            # keep text/plain, chuck the rest
            $this->flatten_parts($child_id, $child, $text, $attachments);
            $plain = true;
            break;
          }
        }

        if (!$plain) {
          # no text/plain, handle the subparts as attachments
          $this->flatten_subparts($part_id, $text, $attachments);
        }
        break;

      case 'mixed':
      default:
        # handle all subparts
        $this->flatten_subparts($part_id, $text, $attachments);
        break;
      }   
      break;

    case 'text':
      # NB: We don't worry about text/html here because Mailman will have
      # already stripped it.

      # Text is appended to the main text.
      $enc = $this->getPartHeader($part_data, 'content-transfer-encoding');
      $body = $this->getPartBody($part_data);
      $body = $this->decode($body, $enc);

      $charset = $part_data['content-charset'];
      if (strtoupper($charset) != 'UTF-8') {
        if (mb_check_encoding($body, $charset)) {
          $body = mb_convert_encoding($body, 'UTF-8', $charset);
        }
        else {
          $body = mb_convert_encoding($body, 'UTF-8');
        }
      }

      $text .= "$body\n";
      break;

    default:
      # Everything else goes into phpBB as an attachment.
      $enc = $this->getPartHeader($part_data, 'content-transfer-encoding');
      $data = $this->getPartBody($part_data);
      $data = $this->decode($data, $enc);    

      $disp = $part_data['content-disposition'];
      if ($disp == 'attachment' || $disp == 'inline') {
        $attachments[] = array( 
          'filename' => $part_data['disposition-filename'],
          'mimetype' => $part_data['content-type'],
          'comment'  => $part_data['content-description'],
          'data'     => $data
        );
      }
    }
  }
}

?>
