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
# Usage: In posting.php, following submit_post():
# 
# require_once('/home/uckelman/site-src/bridge/src/forum_post_send.php'); 
#

try {
  send_post_to_lists($config, $user, $mode, $data, $post_data);
}
catch (Exception $e) {
  trigger_error($e, E_USER_ERROR);
}

function send_post_to_lists($config, $user, $mode, $data, $post_data) {
  require_once('Log.php');
  $logger = &Log::singleton('file', '/var/log/listbridge', 'one');

/*
  print '<p>';
  var_dump($data);
  var_dump($post_data);
  print '</p>';
*/

  require_once('Mail.php');

  require_once(__DIR__ . '/BridgeConf.php');
  require_once(__DIR__ . '/BridgeImpl.php');
  require_once(__DIR__ . '/PhpBB3Conf.php');
  require_once(__DIR__ . '/PhpBB3Impl.php');
  require_once(__DIR__ . '/PhpBB3ToMailman.php');
 
  $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);
  $bridge = new BridgeImpl($db);

  $phpbb = new PhpBB3Impl();

  $mailer = Mail::factory('sendmail');

  $conduit = new PhpBB3ToMailman($bridge, $phpbb, $mailer, $logger);
  $conduit->process($config, $user, $mode, $data, $post_data);
}

?>
