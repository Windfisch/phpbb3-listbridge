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


#
# Usage: In posting.php, following delete_post():
# 
# require_once('/home/uckelman/site-src/bridge/src/forum_post_delete.php'); 
#

try {
  remove_post($post_id);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

function remove_post($postId) {
  require_once('Log.php');
  $logger = &Log::singleton('file', '/var/log/listbridge', 'one');

  require_once(__DIR__ . '/BridgeConf.php');
  require_once(__DIR__ . '/BridgeImpl.php');

  $bdb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);
  $bridge = new BridgeImpl($bdb);

  if ($bridge->removePost($postId)) {
    $logger->info($postId . ' deleted');
  }
  else {
    $logger->info($postId . ' not found, not deleted');
  }
}

?>
