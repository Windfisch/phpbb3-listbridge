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
      array("[quote:2nnqpmcp]Here's some quoty stuff.[quote:2nnqpmcp]Followed by an even deeper quote.[/quote:2nnqpmcp]Followed by more stuff.[/quote:2nnqpmcp]", '2nnqpmcp', "\n> Here's some quoty stuff.\n> > Followed by an even deeper quote.\n> Followed by more stuff.\n", null),
      array("[quote:2k0gcc3t]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vulputate auctor elit, eu auctor tortor mollis id. Phasellus ornare, enim et sollicitudin viverra, diam orci scelerisque orci, ac auctor ante dui vitae urna. Integer vehicula consequat fermentum. Donec euismod, arcu non consectetur elementum, felis nisl lobortis velit, vel dignissim ante purus ac nibh. Quisque at tempus nunc. [quote:2k0gcc3t]Ut ut sapien quis magna ultricies facilisis vitae quis ipsum. Aliquam erat volutpat. Mauris lobortis tempus mi id vestibulum. Proin et leo lectus, nec ullamcorper diam. Integer molestie diam nec risus tincidunt eu cursus erat auctor. Fusce in volutpat enim. Maecenas sit amet lectus justo, in placerat diam. Praesent bibendum mollis dolor, sed convallis neque convallis eget. Ut laoreet auctor magna, in feugiat metus consectetur eget. Donec ornare posuere commodo. [quote:2k0gcc3t]Proin at lorem turpis, sit amet malesuada risus.[/quote:2k0gcc3t]\n\n[quote:2k0gcc3t]Ut porttitor erat iaculis est laoreet eu dapibus dolor condimentum. Nam leo dui, rutrum sit amet mollis ac, pellentesque ut massa. Praesent id rhoncus dui. Nunc quis ante tellus.[/quote:2k0gcc3t] Vivamus nec arcu lorem, ac posuere ipsum. Pellentesque aliquam laoreet purus, sit amet laoreet sapien consectetur mattis. Duis quis ipsum justo, sed volutpat risus. In eget lacus vitae risus pulvinar pellentesque et ut ante.[/quote:2k0gcc3t] Mauris tincidunt, turpis in facilisis posuere, nisl nunc malesuada augue, sed porttitor nunc lorem accumsan purus. Maecenas vestibulum nisl eu risus aliquam porta. Aliquam id consectetur lacus. Aenean non nunc magna, eget tincidunt purus. Praesent consectetur tristique posuere. Nam ac est nisi. Donec tincidunt pellentesque turpis, ut laoreet nisi dapibus sit amet. Nulla quam sapien, cursus a malesuada a, ornare eu sapien.\n\nEtiam nibh metus, consectetur ut laoreet a, pharetra at erat. Fusce lacus diam, consequat sed pellentesque eu, pellentesque quis dolor. Nunc non nisi tellus. Integer nibh magna, aliquet vel accumsan non, euismod non ipsum. Proin sit amet risus venenatis nisl convallis vehicula id eget odio. Nulla non metus eget ligula fringilla euismod.[/quote:2k0gcc3t] Vestibulum hendrerit molestie ipsum. Duis non felis justo, sit amet consectetur orci. Maecenas eu ipsum non eros rutrum elementum a eget arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis tristique suscipit ante eget iaculis. Ut ut odio purus. Sed in lacus nisi. Pellentesque quis velit ut dolor consequat aliquet vel quis quam.\n\n[quote:2k0gcc3t]Suspendisse varius feugiat nibh eget sodales. Cras odio mi, consequat vitae fringilla nec, sollicitudin et orci. Sed tincidunt enim imperdiet nunc porta semper. Nunc vehicula, risus ut ullamcorper luctus, urna ante rutrum risus, id auctor neque lacus nec risus. Vivamus sapien leo, cursus at ullamcorper at, blandit vitae erat. Maecenas egestas, leo at dictum lacinia, nunc sem suscipit turpis, sed fermentum turpis leo vel nisi. Integer sagittis hendrerit purus eu luctus. Mauris bibendum nibh eu velit tincidunt id ultrices erat sodales. Fusce aliquam ultrices risus ullamcorper sollicitudin. Nulla pretium pretium pretium. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ullamcorper lectus sit amet sapien adipiscing non convallis sapien commodo. Nulla laoreet, ipsum eget adipiscing ullamcorper, quam libero vehicula metus, dictum fermentum velit velit non risus. Nullam imperdiet cursus venenatis. Mauris ullamcorper, ligula at faucibus tempor, enim dui iaculis diam, vel fringilla nisl dui sit amet metus. Mauris pharetra volutpat est, et euismod mi malesuada ut. Nunc vehicula pretium mi fringilla volutpat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;\n\nNam pharetra eleifend auctor. Maecenas tempus ornare neque vitae interdum. Mauris ornare, arcu non luctus aliquet, libero mi volutpat diam, eget pretium orci est at nisl. Suspendisse id eros ante. Phasellus dictum ligula sed massa gravida dictum. Sed congue bibendum bibendum. Phasellus egestas elit vel ante tincidunt sit amet luctus dolor faucibus. Cras magna nunc, blandit vel molestie sed, porta non lorem. Quisque quis justo ipsum. Cras faucibus, lacus venenatis luctus tristique, risus elit condimentum elit, sed porttitor dui augue sit amet velit. Etiam consequat condimentum dolor eget auctor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed hendrerit leo quam.[/quote:2k0gcc3t]", '2k0gcc3t', '', null),

      ),
#      array("Foo\n[list:11cx3qbi]\n[*:11cx3qbi] first[/*:11cx3qbi]\n[*:11cx3qbi] second[/*:11cx3qbi][/list:o:11cx3qbi]\nBar", '11cx3qbi', "Foo\n\n * first\n * second\n\nBar", null)
    );
  }
}

?>
