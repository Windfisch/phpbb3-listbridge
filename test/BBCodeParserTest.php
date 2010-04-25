<?php

require_once('PHPUnit/Framework.php');
require_once('src/BBCodeParser.php');

class BBCodeParserTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @dataProvider providerParse
   */
  public function testGetPostId($in, $uid, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $parser = new BBCodeParser();
    $this->assertEquals($expected, $parser->parse($in, $uid));
  }

  public function providerParse() {
    return array(
      array('', '3i2cqt66', '', null),
      array('[b:3i2cqt66]This is a[/b:3i2cqt66] test of [i:3i2cqt66]the BBCode[/i:3i2cqt66] parser. Will [u:3i2cqt66][i:3i2cqt66]it[/i:3i2cqt66][/u:3i2cqt66] parse? Also, throw in some difficult characters: 1 &lt; 2 &lt; 4 &gt; 3.', '3i2cqt66', '__This is a__ test of _the BBCode_ parser. Will __it__ parse? Also, throw in some difficult characters: 1 &lt; 2 &lt; 4 &gt; 3.', null),
      array("Foo\n[list=2:11cx3qbi]\n[*:11cx3qbi] first[/*:m:11cx3qbi]\n[*:11cx3qbi] second[/*:m:11cx3qbi][/list:o:11cx3qbi]\nBar", '11cx3qbi', "Foo\n\n * first\n * second\n\nBar", null)
    );
  }
}

?>
