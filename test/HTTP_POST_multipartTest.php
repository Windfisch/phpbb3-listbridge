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
  public function testBuildDataPart($part, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);

    $this->assertEquals(
      $expected,
      self::getMethod('buildDataPart')->invokeArgs(null, array($part))
    );
  }

  public function providerBuildDataPart() {
    return array(
      array(null, null, 'Exception'),
      array(
        array('name' => 'foo', 'data' => 1),
        "Content-Disposition: form-data; name=\"foo\"\r\n\r\n1\r\n",
        null
      )
    );
  }

  /**
   * @dataProvider providerBuildFilePart
   */
  public function testBuildFilePart($part, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);

    $this->assertEquals(
      $expected,
      self::getMethod('buildFilePart')->invokeArgs(null, array($part))
    );
  }

  public function providerBuildFilePart() {
    return array(
      array(null, null, 'Exception'),
      array(
        array(
          'name'     => 'foo',
          'filename' => 'somename.txt',
          'mimetype' => 'text/plain',
          'charset'  => 'utf-8',
          'encoding' => null,
          'data'     => "blah blah blah\nblah blah blah"
        ),
        "Content-Disposition: form-data; name=\"foo\"; filename=\"somename.txt\"\r\nContent-Type: text/plain; charset=\"utf-8\"\r\n\r\nblah blah blah\nblah blah blah\r\n",
        null
      ),
      array(
        array(
          'name'     => 'foo',
          'filename' => 'somename.png',
          'mimetype' => 'image/png',
          'charset'  => null,
          'encoding' => 'binary',
          'data'     => "blah blah blah\nblah blah blah"
        ),
        "Content-Disposition: form-data; name=\"foo\"; filename=\"somename.png\"\r\nContent-Type: image/png\r\nContent-Transfer-Encoding: binary\r\n\r\nblah blah blah\nblah blah blah\r\n",
        null
      ),
      array(
        array(
          'name'     => 'foo',
          'filename' => 'somename.png',
          'mimetype' => 'image/png',
          'charset'  => null,
          'encoding' => 'base64',
          'data'     => "blah blah blah\nblah blah blah"
        ),
        "Content-Disposition: form-data; name=\"foo\"; filename=\"somename.png\"\r\nContent-Type: image/png\r\nContent-Transfer-Encoding: base64\r\n\r\nYmxhaCBibGFoIGJsYWgKYmxhaCBibGFoIGJsYWg=\r\n\r\n",
        null
      )
    );
  }

  /**
   * @dataProvider providerBuildPost
   */
  public function testBuildPost($parts, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);

    list($boundary, $content) =
      self::getMethod('buildPost')->invokeArgs(null, array($parts));

    $actual = str_replace('boundary', $boundary, $content); 
    $this->assertEquals($expected, $actual);
  }

  public function providerBuildPost() {
    return array(
      array(null, null, 'Exception'),
      array(
        array(
          array(
            'name' => 'foo',
            'data' => 1
          ),
          array(
            'name'     => 'foo',
            'filename' => 'somename.txt',
            'mimetype' => 'text/plain',
            'charset'  => 'utf-8',
            'encoding' => null,
            'data'     => "blah blah blah\nblah blah blah"
          )
        ),
        "--boundary\r\nContent-Disposition: form-data; name=\"foo\"\r\n\r\n1\r\n--boundary\r\nContent-Disposition: form-data; name=\"foo\"; filename=\"somename.txt\"\r\nContent-Type: text/plain; charset=\"utf-8\"\r\n\r\nblah blah blah\nblah blah blah\r\n--boundary--\r\n",
        null
      )
    );
  }
}

?>
