<?php

require_once('PHPUnit/Framework.php');
require_once('src/HTTP_POST_multipart.php');

class HTTP_POST_multipartTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
    # Set all methods to be public so we can test them
    $class = new ReflectionClass('HTTP_POST_multipart');
    foreach ($class->getMethods() as $method) {
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
      HTTP_POST_multipart::buildDataPart($name, $data)
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
