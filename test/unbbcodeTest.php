<?php

require_once('PHPUnit/Framework.php');

class unbbcodeTest extends PHPUnit_Framework_TestCase {
  /**
   * @dataProvider provider_bbcode_second_pass
   */
  public function test_bbcode_second_pass($msg, $uid, $bitfield,
                                          $expected, $ex) {
    if ($ex) $this->setExpectedException($ex);

   /**
    * phpBB3 uses many globals; due to the way tests are run, we cannot
    * easily get these globals into the right scope so that the methods
    * called by our tests can see them. Therefore, we run tests externally
    * and report back on the results.
    */
    $prog = <<<EOF
try {
  require_once("src/unbbcode.php");

  \$msg = "$msg";
  \$uid = "$uid";
  \$bitfield = "$bitfield";

  \$unbbcode = new unbbcode();
  \$unbbcode->bbcode_second_pass(\$msg, \$uid, \$bitfield);
  \$result = serialize(\$msg);
}
catch (Exception \$e) {
  \$result = serialize(\$e);
}

print \$result;
EOF;

    $result = unserialize(exec('php -r \'' . $prog . '\''));

    if ($result instanceof Exception) {
      throw $result;
    }

    $this->assertEquals($expected, $result);
  }

  public function provider_bbcode_second_pass() {
    return array(
      array('[b]Test[/b]', '', false, '<b>Test</b>', null)
    );
  }
}

?>
