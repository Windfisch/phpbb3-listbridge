<?php

require_once('PHPUnit/Framework.php');
require_once('src/Bridge.php');

class BridgeTest extends PHPUnit_Framework_TestCase {

  /**
   * @dataProvider providerGetPostId
   */
  public function testGetPostId($message_id, $expected, $ex) {
    $bridge = new Bridge();
    if ($ex) $this->setExpectedException($ex);
    $this->assertEquals($expected, $bridge->getPostId($message_id));
  }

  public function providerGetPostId() {
    return array(
      array('bogus', null, 'PHPUnit_Framework_Error'), 
    );
  }

  /**
   * @dataProvider providerRegisterMessage
   */
  public function testRegisterMessage($msg, $expected, $ex) {
    $this->markTestIncomplete();
#    $bridge = new Bridge();
#    if ($ex) $this->setExpectedException($ex);
#    $this->assertEquals($expected, $bridge->registerMessage($msg));
  }

  public function providerRegisterMessage() {
    return array(
    );
  }

  /**
   * @dataProvider providerGetDefaultForumId
   */
  public function testGetDefaultForumId($list, $expected, $ex) {
    $bridge = new Bridge();
    if ($ex) $this->setExpectedException($ex);
    $this->assertEquals($expected, $bridge->getDefaultForumId($list));
  }

  public function providerGetDefaultForumId() {
    return array(
      array('bogus', null, 'PHPUnit_Framework_Error'), 
#      array('messages@forums.vassalengine.org', 2, null),
    );
  }


}

?>
