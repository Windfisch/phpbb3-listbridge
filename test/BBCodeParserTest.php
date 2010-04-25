<?php

require_once('PHPUnit/Framework.php');
require_once('src/BBCodeParser.php');

class BBCodeParserTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @dataProvider providerParse
   */
  public function testParse($in, $uid, $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);
    $parser = new BBCodeParser();
    $this->assertEquals($expected, $parser->parse($in, $uid));
  }

  public function providerParse() {
    return array(
      array('', '3i2cqt66', '', null),
      array('[b:3i2cqt66]This is a[/b:3i2cqt66] test of [i:3i2cqt66]the BBCode[/i:3i2cqt66] parser. Will [u:3i2cqt66][i:3i2cqt66]it[/i:3i2cqt66][/u:3i2cqt66] parse? Also, throw in some difficult characters: 1 &lt; 2 &lt; 4 &gt; 3.', '3i2cqt66', "__This is a__ test of _the BBCode_ parser. Will __it__ parse? Also,\nthrow in some difficult characters: 1 < 2 < 4 > 3.", null),
      array("[quote:2nnqpmcp]Here's some quoty stuff.[quote:2nnqpmcp]Followed by an even deeper quote.[/quote:2nnqpmcp]Followed by more stuff.[/quote:2nnqpmcp]", '2nnqpmcp', "\n> Here's some quoty stuff.\n> > Followed by an even deeper quote.\n> Followed by more stuff.\n", null),
      array('This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line.[quote:2nnqpmcp]This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line.[quote:2nnqpmcp]This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line. This is a rather long line.[/quote:2nnqpmcp][/quote:2nnqpmcp]', '2nnqpmcp', "This is a rather long line. This is a rather long line. This is a rather\nlong line. This is a rather long line. This is a rather long line. This\nis a rather long line.\n> This is a rather long line. This is a rather long line. This is a\n> rather long line. This is a rather long line. This is a rather long\n> line. This is a rather long line.\n> > This is a rather long line. This is a rather long line. This is a\n> > rather long line. This is a rather long line. This is a rather long\n> > line. This is a rather long line.\n> \n", null),

#      array("Foo\n[list:11cx3qbi]\n[*:11cx3qbi] first[/*:11cx3qbi]\n[*:11cx3qbi] second[/*:11cx3qbi][/list:o:11cx3qbi]\nBar", '11cx3qbi', "Foo\n\n * first\n * second\n\nBar", null)
    );
  }
}

?>
