<?php

require_once('PHPUnit/Framework.php');
require_once('src/HTTP_POST_multipart.php');

class HTTP_POST_multipartTest extends PHPUnit_Framework_TestCase {

  protected static $_class;

  public static function setUpBeforeClass() {
    # Set all methods to be public so we can test them
    self::$_class = new ReflectionClass('HTTP_POST_multipart');
    foreach (self::$_class->getMethods() as $method) {
      $method->setAccessible(true);
    }
  }

  /**
   * @dataProvider providerBuildDataPart
   */
  public function testBuildDataPart($name, $data, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $this->assertEquals(
      $expected,
      self::$_class->getMethod('bulidDataPart', array($name, $data))
    );
  }

  public function providerBuildDataPart() {
    return array(
      array(null, null, null, 'Exception'),
      array('foo', 1, "Content-Disposition: form-data; name=\"foo\"\r\n\r\n1\r\n", null)
    );
  }

}

?>
