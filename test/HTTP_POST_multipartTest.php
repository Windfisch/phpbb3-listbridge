<?php

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

    $expected = str_replace('boundary', $boundary, $expected); 
    $this->assertEquals($expected, $content);
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
