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
$logger = &Log::singleton('file', '/var/log/listbridge', 'one');

try {
  if (!isset($_POST['message'])) {
    throw new Exception('No message in POST');
  }

  require_once(__DIR__ . '/BridgeConf.php');
  require_once(__DIR__ . '/BridgeImpl.php');
  require_once(__DIR__ . '/MailmanMessage.php');
  require_once(__DIR__ . '/MailmanToPhpBB3.php');
  require_once(__DIR__ . '/PhpBB3Conf.php');
  require_once(__DIR__ . '/PhpBB3Impl.php');

  $msg = new MailmanMessage($_POST['message']);

  $bdb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB, DB_USER, DB_PASS);
  $bridge = new BridgeImpl($bdb);

  $phpbb = new PhpBB3Impl();

  $conduit = new MailmanToPhpBB3($bridge, $phpbb, $logger);
  $conduit->process($msg);
}
catch (Exception $e) {
  $logger->err($e);
  error_log($e);
}

?>
