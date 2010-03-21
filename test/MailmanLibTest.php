<?php

require_once('PHPUnit/Framework.php');
require_once('src/MailmanLib.php');

class MailmanLibTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @dataProvider provider_read_raw_message
   */
  public function test_read_raw_message($url, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $this->assertEquals($expected, read_raw_message($url));
  }

  public function provider_read_raw_message() {
    return array(
      array(__DIR__ . '/empty', null, 'PHPUnit_Framework_Error'),
      array(__DIR__ . '/bougs', null, 'PHPUnit_Framework_Error'),
      array(__DIR__ . '/1',     file_get_contents(__DIR__ . '/1'), null),
    );
  }
}

?>
