<?php

require_once('PHPUnit/Framework.php');

require_once('src/EmailMessage.php');

class EmailMessageTest extends PHPUnit_Framework_TestCase {

  public function provider() {
    return array(
      array(array(
        'file'        => file_get_contents(__DIR__ . '/1'),
        'source'      => '',
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
    $msg = new EmailMessage($expected['file']);
    $this->assertEquals($expected['from'], $msg->getFrom());
  }

  /**
   * @dataProvider provider
   */
  public function testGetSubject($expected) {
    $msg = new EmailMessage($expected['file']);
    $this->assertEquals($expected['subject'], $msg->getSubject());
  }
  
  /**
   * @dataProvider provider
   */
  public function testGetMessageId($expected) {
    $msg = new EmailMessage($expected['file']);
    $this->assertEquals($expected['message_id'], $msg->getMessageId());
  }

  /**
   * @dataProvider provider
   */
  public function testGetInReplyTo($expected) {
    $msg = new EmailMessage($expected['file']);
    $this->assertEquals($expected['in_reply_to'], $msg->getInReplyTo());
  }

  /**
   * @dataProvider provider
   */
  public function testGetReferences($expected) {
    $msg = new EmailMessage($expected['file']);
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
