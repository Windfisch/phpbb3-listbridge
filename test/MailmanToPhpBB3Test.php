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
require_once(__DIR__ . '/../src/MailmanMessage.php');
require_once(__DIR__ . '/../src/MailmanToPhpBB3.php');
require_once(__DIR__ . '/../src/PhpBB3.php');

class MailmanToPhpBB3Test extends PHPUnit_Framework_TestCase {

  public function testProcess() {
    $this->markTestIncomplete();

    $bridge = $this->getMock('Bridge');
    $bridge->expects($this->once())
           ->method('registerByMessageId')
           ->with('<20100302094228.33F0310091@charybdis.ellipsis.cx>',
                  '<1267473003.m2f.17543@www.vassalengine.org>')
           ->will($this->returnValue(1));


 
    $phpbb = $this->getMock('PhpBB3');

    $logger = &Log::singleton('null');

    $conduit = new MailmanToPhpBB3($bridge, $phpbb, $logger);

    $msg = new MailmanMessage(file_get_contents(__DIR__ . '/1'));
    $conduit->process($msg);

/*
    $phpbb->expects($this->once())
           ->method('getPostTime')
           ->with($postId)
           ->will($this->returnValue( ));
    $phpbb->expects($this->once())
          ->method('getAttachmentData')
          -
*/
  }
}

?>
