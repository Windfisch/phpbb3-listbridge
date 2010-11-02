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
require_once('src/BridgeImpl.php');

class BridgeImplTest extends PHPUnit_Framework_TestCase {

/*
  protected $db;

  protected function setUp() {
    $this->db = new PDO('mysql:host=localhost;dbname=test');
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //
    // Build posts table
    //
    $this->db->exec('DROP TABLE IF EXISTS posts');

    $this->db->exec(
      'CREATE TABLE posts (' .
      'post_id MEDIUMINT UNSIGNED, ' .
      'message_id VARCHAR(255) NOT NULL, ' .
      'in_reply_to VARCHAR(255), ' .
      'edit_id MEDIUMINT NOT NULL AUTO_INCREMENT, ' .
      'UNIQUE KEY (message_id), ' .
      'PRIMARY KEY (edit_id), ' .
      'INDEX (post_id))'
    );

    $this->db->exec(
      'INSERT INTO posts (post_id, message_id, in_reply_to) ' .
      'VALUES (' .
        '1, ' .
        '"<20100302094228.33F0310091@charybdis.ellipsis.cx>", ' .
        '"<1267473003.m2f.17543@www.vassalengine.org>"' .
      ')'
    );

    //
    // Build forums table
    //
    $this->db->exec('DROP TABLE IF EXISTS forums');

    $this->db->exec(
      'CREATE TABLE forums (' .
      'list_name VARCHAR(255) NOT NULL, ' .
      'forum_id MEDIUMINT UNSIGNED NOT NULL, ' .
      'PRIMARY KEY (list_name), ' .
      'INDEX (forum_id))'
    );

    $this->db->exec(
      'INSERT INTO forums (list_name, forum_id) ' .
      'VALUES (' .
        '"messages@test.nomic.net", 2' .
      ')'
    );
  }

  protected function tearDown() {
    $this->db = null;
  }
*/

  protected $globalsBackup = false;

  protected $db;

  public function setUp() {
    $this->markTestSkipped();

    $this->db = $this->getMock(
      'PDO', array('__construct', 'quote', 'query'), array(), 'TestPDO', false
    );
  } 

  public function testGetPostId2() {
    $message_id = 1;

/*
    $db->expects($this->once())
       ->method('quote')
       ->with($message_id)
       ->will($this->returnValue("'$message_id'"));
    $db->expects($this->once())
       ->method('query')
       ->will($this->returnValue($message_id));
*/

//    $bridge = new BridgeImpl($db);
//    $this->assertEquals($message_id, $bridge->getPostId($message_id));
  }



  /**
   * @dataProvider providerGetPostId
   */
/*
  public function testGetPostId($message_id, $expected, $ex) {
    $db = $this->getMock('PDO', array('foo'), array(), '', false);
    $db->expects($this->once())
       ->method('quote')
       ->with($message_id)
       ->will($this->returnValue("'$message_id'"));
    $db->expects($this->once())
       ->method('query')
       ->will($this->returnValue($message_id));

    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($db);
    $this->assertEquals($expected, $bridge->getPostId($message_id));
  }

  public function providerGetPostId() {
    return array(
      array(null, null, 'Exception'), 
      array('bogus', false, null), 
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 1, null),
    );
  }
*/

  /**
   * @dataProvider providerGetMessageId
   */
/*
  public function testGetMessageId($post_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals($expected, $bridge->getMessageId($post_id));
  }

  public function providerGetMessageId() {
    return array(
      array(null, null, 'Exception'), 
      array(0, false, null), 
      array(1, '<20100302094228.33F0310091@charybdis.ellipsis.cx>', null),
    );
  }
*/
  /**
   * @dataProvider providerReserveEditId
   */
/*
  public function testReserveEditId($postId, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals($expected, $bridge->reserveEditId($postId));
  }

  public function providerReserveEditId() {
    return array(
      array(null, null, 'Exception'),
      array(1, 2, null),
      array(2, 2, null) 
    );
  }
*/
  /**
   * @dataProvider providerRegisterByEditId
   */
/*
  public function testRegisterByEditId($editId, $messageId, $inReplyTo,
                                       $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals(
      $expected,
      $bridge->registerByEditId($editId, $messageId, $inReplyTo)
    );
  }

  public function providerRegisterByEditId() {
    return array(
      array(null, null, null, null, 'Exception'),
      array(
        2,
        '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        false,
        null
      ),
      array(
        1,
        '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        true,
        null
      ),
    );
  }
*/

  /**
   * @dataProvider providerRegisterByMessageId
   */
/*
  public function testRegisterByMessageId($messageId, $inReplyTo,
                                          $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals(
      $expected,
      $bridge->registerByMessageId($messageId, $inReplyTo)
    );
  }

  public function providerRegisterByMessageId() {
    return array(
      array(null, null, null, 'Exception'),
      array(
        '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        false,
        null
      ),
      array(
        '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        false,
        null
      ),
      array(
        '<10100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        2,
        null
      ),
      array(
        '<10100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        2,
        null
      )
    );
  }
*/

  /**
   * @dataProvider providerUnregisterMessage
   */
/*
  public function testUnregisterMessage($editId, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $bridge->unregisterMessage($editId);
  }

  public function providerUnregisterMessage() {
    return array(
      array(null, 'Exception'),
      array(1, null),
      array(2, 'Exception')
    );
  }
*/

  /**
   * @dataProvider providerRemovePost
   */
/*
  public function testRemovePost($postId, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $bridge->removePost($postId);
  }

  public function providerRemovePost() {
    return array(
      array(null, 'Exception'),
      array(1, null),
      array(2, 'Exception')
    );
  }
*/

  /**
   * @dataProvider providerGetDefaultForumId
   */
/*
  public function testGetDefaultForumId($list, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals($expected, $bridge->getDefaultForumId($list));
  }

  public function providerGetDefaultForumId() {
    return array(
      array(null, null, 'Exception'),
      array('bogus', false, null),
      array('messages@test.nomic.net', 2, null),
    );
  }
*/

  /**
   * @dataProvider providerGetLists
   */
/*
  public function testGetLists($forumId, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);
    $this->assertEquals($expected, $bridge->getLists($forumId));
  }

  public function providerGetLists() {
    return array(
      array(null, null, 'Exception'),
      array(1, array(), null),
      array(2, array('messages@test.nomic.net'), null)
    );
  }
*/

  /**
   * @dataProvider providerSetPostId
   */
/*
  public function testSetPostId($messageId, $postId, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new BridgeImpl($this->db);

    $bridge->setPostId($messageId, $postId);
    $this->assertEquals($bridge->getPostId($messageId), $postId);
  }

  public function providerSetPostId() {
    return array(
      array(null, 1, 'Exception'),
      array('<10100302094228.33F0310091@charybdis.ellipsis.cx>', null, 'Exception'),
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 2, null),
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 3, null),
    );
  }
*/
}

?>
