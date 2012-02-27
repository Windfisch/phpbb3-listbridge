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
      array("Here is a list:\n[list:wjve1skj]\n[*:wjve1skj] first item[/*:m:wjve1skj]\n[*:wjve1skj] second item[/*:m:wjve1skj]\n[*:wjve1skj] third item\n[list=1:wjve1skj]\n[*:wjve1skj] a subsidiary item[/*:m:wjve1skj]\n[*:wjve1skj] another subsidiary item[/*:m:wjve1skj][/list:o:wjve1skj][/*:m:wjve1skj]\n[*:wjve1skj] the last item[/*:m:wjve1skj][/list:u:wjve1skj]\nAnd some more text.", 'wjve1skj', "Here is a list:\n\n  * first item\n  * second item\n  * third item\n    1. a subsidiary item\n    2. another subsidiary item\n  * the last item\n\nAnd some more text.", null),
      array("Here's a BBCode URL: [url:3i2cqt66=http://www.vassalengine.org]http://www.vassalengine.org[/url:3i2cqt66]. Will it be converted?", '3i2cqt66', "Here's a BBCode URL: http://www.vassalengine.org[1]. Will it be\nconverted?\n\n[1] http://www.vassalengine.org\n", null),
      array("Here's a non-BBCode URL: <!-- m --><a class=\"postlink\" href=\"http://www.vassalengine.org\">http://www.vassalengine.org</a><!-- m -->. Will it be converted?", '3i2cqt66', "Here's a non-BBCode URL: http://www.vassalengine.org[1]. Will it be\nconverted?\n\n[1] http://www.vassalengine.org\n", null),
#      array("Foo\n[list:11cx3qbi]\n[*:11cx3qbi] first[/*:11cx3qbi]\n[*:11cx3qbi] second[/*:11cx3qbi][/list:o:11cx3qbi]\nBar", '11cx3qbi', "Foo\n\n * first\n * second\n\nBar", null)
    );
  }
}

?>
