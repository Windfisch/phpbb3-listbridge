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

class PhpBB3Message implements Message {
  
  protected $user;
  protected $post;

  public function __construct($user, $post) {
    $this->user = $user;
    $this->post = $post;
  }

  public function getSource() {
    return null;
  }

  public function getPostId() {
    return $this->post['post_id'];
  }

  public function getFrom() {
    return '"' . $this->user->data['username'] . '" <'
               . $this->user->data['user_email'] . '>';
  }

  public function getSubject() {
    return $this->post['post_subject']; 
  }
  
  public function getMessageId() {
    return null;
  }

  public function getInReplyTo() {
    return null;
  }

  public function getReferences() {
    return null;
  }
}

?>
