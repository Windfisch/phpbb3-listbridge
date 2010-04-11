<?php

require_once('PHPUnit/Framework.php');
require_once('src/Bridge.php');

class BridgeTest extends PHPUnit_Framework_TestCase {

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
      'refs BLOB, ' .
      'UNIQUE KEY (post_id), ' .
      'UNIQUE KEY (message_id))'
    );

    $this->db->exec(
      'INSERT INTO posts (post_id, message_id, in_reply_to, refs) ' .
      'VALUES (' .
        '1, ' .
        '"<20100302094228.33F0310091@charybdis.ellipsis.cx>", ' .
        '"<1267473003.m2f.17543@www.vassalengine.org>", ' .
        '"<1267171317.m2f.17507@www.vassalengine.org> <1267473003.m2f.17543@www.vassalengine.org>"' .
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
        '"messages@forums.vassalengine.org", 2' .
      ')'
    );
  }

  protected function tearDown() {
    $this->db = null;
  }

  /**
   * @dataProvider providerGetPostId
   */
  public function testGetPostId($message_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new Bridge($this->db);
    $this->assertEquals($expected, $bridge->getPostId($message_id));
  }

  public function providerGetPostId() {
    return array(
      array('bogus', null, 'PHPUnit_Framework_Error'), 
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 1, null),
    );
  }

  /**
   * @dataProvider providerGetMessageId
   */
  public function testGetMessageId($post_id, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new Bridge($this->db);
    $this->assertEquals($expected, $bridge->getMessageId($post_id));
  }

  public function providerGetMessageId() {
    return array(
      array('bogus', null, 'PHPUnit_Framework_Error'), 
      array(1, '<20100302094228.33F0310091@charybdis.ellipsis.cx>', null),
    );
  }

  /**
   * @dataProvider providerRegisterMessage
   */
  public function testRegisterMessage($messageId, $inReplyTo, $refs,
                                      $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new Bridge($this->db);
    $this->assertEquals(
      $expected,
      $bridge->registerMessage($messageId, $inReplyTo, $refs)
    );
  }

  public function providerRegisterMessage() {
    return array(
      array('bogus', null, null, null, null, 'PDOException'),
      array(
        '<20100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        null,
        null,
        'PDOException'
      ),
      array(
        '<10100302094228.33F0310091@charybdis.ellipsis.cx>',
        null,
        null,
        true,
        null,
      )    
    );
  }

  /**
   * @dataProvider providerGetDefaultForumId
   */
  public function testGetDefaultForumId($list, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new Bridge($this->db);
    $this->assertEquals($expected, $bridge->getDefaultForumId($list));
  }

  public function providerGetDefaultForumId() {
    return array(
      array('bogus', null, 'PHPUnit_Framework_Error'), 
      array('messages@forums.vassalengine.org', 2, null),
    );
  }

  /**
   * @dataProvider providerSetPostId
   */
  public function testSetPostId($messageId, $postId, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $bridge = new Bridge($this->db);

    $bridge->setPostId($messageId, $postId);
    $this->assertEquals($bridge->getPostId($messageId), $postId);
  }

  public function providerSetPostId() {
    return array(
      array(null, null, 'PHPUnit_Framework_Error'),
      array('<10100302094228.33F0310091@charybdis.ellipsis.cx>', null, 'PDOException'),
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 2, null),
      array('<20100302094228.33F0310091@charybdis.ellipsis.cx>', 3, null),
    );
  }
}

?>
