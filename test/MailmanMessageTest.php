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

require_once('PHPUnit/Framework.php');
require_once('src/MailmanMessage.php');

class MailmanMessageTest extends PHPUnit_Framework_TestCase {
  public function provider() {
    return array(
      array(array(
        'data'        => file_get_contents(__DIR__ . '/1'),
        'source'      => 'messages@forums.vassalengine.org',
        'post_id'     => '',
        'from'        => 'uckelman@nomic.net',
        'subject'     => 'Re: [Developers]Re: Adding developers?',
        'message_id'  => '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        'in_reply_to' => '<1267473003.m2f.17543@www.vassalengine.org>',
        'references'  => '<1267171317.m2f.17507@www.vassalengine.org> <1267473003.m2f.17543@www.vassalengine.org>',
        'body'        => ''
      ))
    );
  }

  protected function buildMessage($params) {
    return new MailmanMessage($params['data']);
  }

  /**
   * @dataProvider provider
   */
  public function testGetSource($expected) {
    $this->markTestIncomplete();
  }

  /**
   * @dataProvider provider
   */
  public function testGetPostId($expected) {
    $this->markTestIncomplete();
  }

  /**
   * @dataProvider provider
   */
  public function testGetFrom($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['from'], $msg->getFrom());
  }

  /**
   * @dataProvider provider
   */
  public function testGetSubject($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['subject'], $msg->getSubject());
  }
  
  /**
   * @dataProvider provider
   */
  public function testGetMessageId($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['message_id'], $msg->getMessageId());
  }

  /**
   * @dataProvider provider
   */
  public function testGetInReplyTo($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['in_reply_to'], $msg->getInReplyTo());
  }

  /**
   * @dataProvider provider
   */
  public function testGetReferences($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['references'], $msg->getReferences());
  }

  /**
   * @dataProvider provider
   */
  public function testGetBody($expected) {
    $this->markTestIncomplete();
  }
}

?>
