<?php

require_once('PHPUnit/Framework.php');
require_once('src/HTTP_POST_multipart.php');

class HTTP_POST_multipartTest extends PHPUnit_Framework_TestCase {

  protected static function getMethod($name) {
    $class = new ReflectionClass('HTTP_POST_multipart');
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  /**
   * @dataProvider providerBuildDataPart
   */
  public function testBuildDataPart($name, $data, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);

    $this->assertEquals(
      $expected,
      self::getMethod('buildDataPart')->invokeArgs(null, array($name, $data))
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
