<?php

require_once('PHPUnit/Framework.php');
require_once('src/PhpBB3Message.php');

class PhpBB3MessageTest extends PHPUnit_Framework_TestCase {

  public function provider() {
    return array(
/*
      array(array(
        'data'        => '',
        'source'      => '',
        'post_id'     => '',
        'from'        => '',
        'subject'     => '',
        'message_id'  => '',
        'in_reply_to' => '',
        'references'  => '',
        'body'        => ''
      ))
*/
    );
  }

  protected function buildMessage($params) {
    return new PhpBB3Message();   
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
