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
        'flattened'   => array(
          file_get_contents(__DIR__ . '/1_flat'),
          array()
        )
      )),
      array(array(
        'data'        => file_get_contents(__DIR__ . '/377'),
        'source'      => 'messages@vassalengine.org',
        'post_id'     => '',
        'from'        => 'mkiefte@dal.ca',
        'subject'     => 'Re: [messages] [Module Design] Cropping and using Transparency',
        'message_id'  => '<AANLkTi=BzoLFm5L5DwdxHwNr9tkRpHz+3O8Z6akU85HZ@mail.gmail.com>',
        'in_reply_to' => '<1286462494.20188.1569.bridge@www.vassalengine.org>',
        'references'  => '<1286462494.20188.1569.bridge@www.vassalengine.org>',
        'flattened'   => array(
          file_get_contents(__DIR__ . '/377_flat'),
          array()
        )
      )),
      array(array(
        'data'        => file_get_contents(__DIR__ . '/287'),
        'source'      => 'messages@vassalengine.org',
        'post_id'     => '',
        'from'        => 'pgeerkens@hotmail.com',
        'subject'     => '[messages] Edit: [Developers] Re: Wannabe VASSAL developer has setup question',
        'message_id'  => '<1285379813.20024.1394.bridge@www.vassalengine.org>',
        'in_reply_to' => '<1285379627.20024.1393.bridge@www.vassalengine.org>',
        'references'  => null, 
        'flattened'   => array(
          file_get_contents(__DIR__ . '/287_flat'),
          array(
            array( 
              'filename' => 'Eclispse2.PNG',
              'mimetype' => 'image/png',
              'comment'  => '',
              'data'     => file_get_contents(__DIR__ . '/Eclispse2.PNG')
            ),
            array( 
              'filename' => 'Eclipse.PNG',
              'mimetype' => 'image/png',
              'comment'  => '',
              'data'     => file_get_contents(__DIR__ . '/Eclipse.PNG')
            )
          )
        )
      )),
      array(array(
        'data'        => file_get_contents(__DIR__ . '/372'),
        'source'      => 'messages@vassalengine.org',
        'post_id'     => '',
        'from'        => 'uckelman@nomic.net',
        'subject'     => 'Re: [messages] [Developers] Re: determining how much heap a BufferedImage uses',
        'message_id'  => '<20101110174225.587BD100B5@charybdis.ellipsis.cx>',
        'in_reply_to' => '<1289408163.20521.1933.bridge@www.vassalengine.org>',
        'references'  => '<1286189102.20134.1513.bridge@www.vassalengine.org> <1289408163.20521.1933.bridge@www.vassalengine.org>',
        'flattened'   => array(
          file_get_contents(__DIR__ . '/372_flat'),
          array()
        )
      )),
      array(array(
        'data'        => file_get_contents(__DIR__ . '/195'),
        'source'      => 'messages@vassalengine.org',
        'post_id'     => '',
        'from'        => 'wojciech.meyer@googlemail.com',
        'subject'     => 'Re: [messages] [Feature Requests] Re: Fix private message windows stealing focus',
        'message_id'  => '<87hbezwt40.fsf@gmail.com>',
        'in_reply_to' => '<20101130000631.16061100CD@charybdis.ellipsis.cx>',
        'references'  => '<1291044119.20746.2185.bridge@www.vassalengine.org> <87vd3frios.fsf@gmail.com> <20101129203806.A2B15100CD@charybdis.ellipsis.cx> <87ipzfrgwp.fsf@gmail.com> <20101129210745.5A113100CD@charybdis.ellipsis.cx> <87mxorwv05.fsf@gmail.com> <20101130000631.16061100CD@charybdis.ellipsis.cx>',
        'flattened'   => array(
          file_get_contents(__DIR__ . '/195_flat'),
          array()
        )
      )),
    );
  }

  protected function buildMessage($params) {
    return new MailmanMessage($params['data']);
  }

  /**
   * @dataProvider provider
   */
  public function testGetSource($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['source'], $msg->getSource());
  }

  /**
   * @dataProvider provider
   */
  public function testGetPostId($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['post_id'], $msg->getPostId());
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
  public function testGetFlattenedParts($expected) {
    $msg = $this->buildMessage($expected);
    $this->assertEquals($expected['flattened'], $msg->getFlattenedParts());
  }
}

?>
