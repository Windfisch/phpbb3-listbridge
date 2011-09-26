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

interface PhpBB3 {
  public function getUserId($from);

  public function getUserName($id);

  public function getTopicAndForumIds($post_id);

  public function forumExists($forumId);

  public function topicStatus($topicId);

  public function getPostTime($postId);

  public function getAttachmentData($attachId);

  public function postMessage($postType, $forumId, $topicId, $msg); 

  public function addAttachment($userId, $filename, $comment,
                                                    $mimetype, $data);
}

?>
