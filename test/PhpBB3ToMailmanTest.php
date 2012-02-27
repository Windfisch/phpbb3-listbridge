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

require_once(__DIR__ . '/../src/Bridge.php');
require_once(__DIR__ . '/../src/PhpBB3.php');
require_once(__DIR__ . '/../src/PhpBB3ToMailman.php');

class User {
  public $data = array();

  public function __construct($userName, $userEmail) {
    $this->data['username'] = $userName;
    $this->data['user_email'] = $userEmail;
  }
}

class PhpBB3ToMailmanTest extends PHPUnit_Framework_TestCase {

  public function testProcessPost() {
    $this->markTestIncomplete();

    $config = array('upload_path' => '');
    $user = new User('Joel Uckelman', 'uckelman@nomic.net');
    $mode = 'post';
    $data = array(
      'attachment_data' => array(),
      'bbcode_uid' => '3i2cqt66',
      'forum_id' => '3',
      'post_id' => '1',
      'message' => '[b:3i2cqt66]This is a[/b:3i2cqt66] test of [i:3i2cqt66]the BBCode[/i:3i2cqt66] parser. Will [u:3i2cqt66][i:3i2cqt66]it[/i:3i2cqt66][/u:3i2cqt66] parse? Also, throw in some difficult characters: 1 &lt; 2 &lt; 4 &gt; 3.', 
      'topic_first_post_id' => ''
    );
    $post_data = array(
      'forum_name' => 'Test forum',
      'post_subject' => 'This is a test post'
    );

    $_SERVER['SERVER_NAME'] = 'vassalengine.org/forum';
    $_SERVER['SCRIPT_NAME'] = '/var/www/forum/viewtopic.php';

    $bridge = $this->getMock('Bridge');
    $bridge->expects($this->once())
           ->method('getLists')
           ->with($data['forum_id'])
           ->will($this->returnValue(array('messages@vassalengine.org')));
    $bridge->expects($this->once())
           ->method('reserveEditId')
           ->with($data['post_id'])
           ->will($this->returnValue(1));
    $bridge->expects($this->once())
           ->method('registerByEditId')
           ->with(1, $this->anything(), $this->anything())
           ->will($this->returnValue(true));
 
    $phpbb = $this->getMock('PhpBB3');
    $phpbb->expects($this->once())
           ->method('getPostTime')
           ->with($data['post_id'])
           ->will($this->returnValue(1288562162));
  
    $mailer = $this->getMock('Mail');
    $mailer->expects($this->once())
           ->method('send')
           ->with('messages@vassalengine.org',
                  $this->anything(), $this->anything())
           ->will($this->returnValue(null));
 
    $logger = &Log::singleton('null'); 

    $conduit = new PhpBB3ToMailman($bridge, $phpbb, $mailer, $logger);

   
    $conduit->process($config, $user, $mode, $data, $post_data);
  }
}

?>
