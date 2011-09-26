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

interface Bridge {
  public function getPostId($messageId); 

  public function getMessageId($postId);

  public function setPostId($messageId, $postId);

  public function getDefaultForumId($list);

  public function getLists($forumId);

  public function reserveEditId($postId); 

  public function registerByEditId($editId, $messageId, $inReplyTo); 

  public function registerByMessageId($messageId, $inReplyTo);

  public function unregisterMessage($editId);
}

?>
